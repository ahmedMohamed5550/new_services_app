<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\ProjectLike;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectLikesResource;

class ProjectController extends Controller
{

    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/projects/index",
     *     summary="List all projects",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Show all projects successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Show All projects successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No projects found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No projects found")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $projects = Project::with(['service', 'user', 'section'])->get();

        if (count($projects) > 0) {
            return $this->apiResponse('Show All projects successfully', 200, ProjectResource::collection($projects));
        }

        return $this->apiResponse('No projects found', 404);
    }

    /**
     * @OA\Post(
     *     path="/api/projects/create",
     *     summary="Create a new project",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"section_id", "service_id", "user_id", "desc", "city"},
     *                     @OA\Property(
     *                         property="section_id",
     *                         type="integer",
     *                         description="ID of the section",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="service_id",
     *                         type="integer",
     *                         description="ID of the service",
     *                         example=2
     *                     ),
     *                     @OA\Property(
     *                         property="user_id",
     *                         type="integer",
     *                         description="ID of the user",
     *                         example=3
     *                     ),
     *                     @OA\Property(
     *                         property="desc",
     *                         type="string",
     *                         description="Description of the project",
     *                         example="This is a project description."
     *                     ),
     *                     @OA\Property(
     *                         property="city",
     *                         type="string",
     *                         description="City related to the project",
     *                         example="New York"
     *                     ),
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function store(CreateProjectRequest $request)
    {
        $project = Project::create($request->validated());

        $project->load(['service', 'user', 'section']);
        return $this->apiResponse('Project created successfully', 201, new ProjectResource($project));
    }

    /**
     * @OA\Get(
     *     path="/api/projects/show/{id}",
     *     summary="Show project details",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project details"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No project found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $project = Project::findOrFail($id);

        if (!$project) {
            return $this->apiResponse('No project found', 404);
        }

        $project->load(['service', 'user', 'section' , 'comments']);

        // Increment the view count
        $project->increment('views');
        return $this->apiResponse('Project details', 200, new ProjectResource($project));
    }

    /**
     * @OA\Post(
     *     path="/api/projects/update/{id}",
     *     summary="Update a project",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(
     *                         property="desc",
     *                         type="string",
     *                         description="Description of the project",
     *                         example="Updated project description."
     *                     ),
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No project found")
     *         )
     *     )
     * )
     */

    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);

        if (!$project) {
            return $this->apiResponse('No project found', 404);
        }

        $project->update($request->validated());
        $project->load(['service', 'user', 'section']);
        return $this->apiResponse('Project updated successfully', 200, new ProjectResource($project));
    }

    /**
     * @OA\Delete(
     *     path="/api/projects/destroy/{id}",
     *     summary="Delete a project",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No project found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        if (!$project) {
            return $this->apiResponse('No project found', 404);
        }

        $project->delete();
        return $this->apiResponse('Project deleted successfully', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/projects/{projectId}/like/{userId}",
     *     summary="Like a project",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="projectId",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID of the user who likes the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project liked successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project liked successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User already liked this project",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User already liked this project")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No project found")
     *         )
     *     )
     * )
     */


    // Method to like and unlike a project
    public function like($projectId, $userId)
    {
        $project = Project::findOrFail($projectId);

        $existingLike = ProjectLike::where('user_id', $userId)->where('project_id', $project->id)->first();

        // If the user has already liked the project, remove the like
        if ($existingLike) {
            $existingLike->delete();
            return $this->apiResponse('User unliked the project', 200);
        }

        ProjectLike::create([
            'user_id' => $userId,
            'project_id' => $project->id,
        ]);

        return $this->apiResponse('Project liked successfully', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/projects/total-likes/{projectId}",
     *     summary="Get total likes for a project",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="projectId",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project details"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No project found")
     *         )
     *     )
     * )
     */

    public function getTotalLikes($projectId)
    {
        $project = Project::findOrFail($projectId);

        if (!$project) {
            return $this->apiResponse('project not found', 404);
        }

        $totalLikes = $project->likes()->count();

        $likedUsers = $project->likes()->with('user')->get();

        return $this->apiResponse('Total likes for the project', 200, [
            'project_id' => $projectId,
            'total_likes' => $totalLikes,
            'liked_users' => ProjectLikesResource::collection($likedUsers),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/projects/{project_id}/comment/{user_id}",
     *     summary="Create a new comment",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="project_id",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"content"},
     *                     @OA\Property(
     *                         property="content",
     *                         type="string",
     *                         description="Description of the comment",
     *                         example="This is a project comment."
     *                     ),
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="comment created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="comment created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

     public function createComment(CommentRequest $request , $project_id , $user_id)
     {

        $project = Project::find($project_id);
        if(!$project)
        {
            return $this->apiResponse('Not Found' , 404);
        }

        $validated_data = $request->validated();

        $comment = Comment::create([
            'project_id'=>$project_id,
            'user_id'=>$user_id,
            'content'=>$request->content,
        ]);
        $comment->load('user');

        if($comment)
        {
            return $this->apiResponse('comment created successfully' , 200 , new CommentResource($comment));
        }
        return $this->apiResponse('Error Created Commnet' , 400);

     }

      /**
     * @OA\Get(
     *     path="/api/projects/total-comments/{projectId}",
     *     summary="Get total comments for a project",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="projectId",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project details"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No project found")
     *         )
     *     )
     * )
     */

     public function showComment($projectId)
     {
        $project = Project::find($projectId);
        if(!$project)
        {
            return $this->apiResponse('Project Not Found' , 404);
        }

        $comments = $project->comments()->with('user')->get();
        $count_comment = $project->comments()->count();


        return $this->apiResponse('Total comments for the project', 200, [
            'project_id' => $projectId,
            'total_comment' => $count_comment,
            'comments' => CommentResource::collection($comments),
        ]);

     }
     

     /**
     * @OA\Post(
     *     path="/api/projects/comment/{comment_id}/update/{user_id}",
     *     summary="Update a comment",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         description="ID of the comment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(
     *                         property="content",
     *                         type="string",
     *                         description="Description of the comment",
     *                         example="Updated comment description."
     *                     ),
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="comment updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="comment updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="comment not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No project found")
     *         )
     *     )
     * )
     */

     public function updateComment(CommentRequest $request , $comment_id , $user_id)
     {
        $comment = Comment::find($comment_id);
        if(!$comment)
        {
            return $this->apiResponse('Not Found' , 404);
        }

       

        $userId = $comment->user->id;
        if($user_id == $userId)
        {
            $udpated_comment = $comment->update($request->validated());
            $new_comment = Comment::with('user')->find($comment_id);
            return $this->apiResponse('comment updated successfully' , 200 ,new CommentResource($new_comment));
        }

        return $this->apiResponse('Cant update this comment' , 400);

     }

     /**
     * @OA\Post(
     *     path="/api/projects/comment/delete/{comment_id}/{user_id}",
     *     summary="Delete Comment",
     *     tags={"Project"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         description="ID of the comment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="commnet deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="commnet deleted successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="cant delete this comment",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="cant delete this comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="comment not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No comment found")
     *         )
     *     )
     * )
     */


     public function deleteComment(Request $request , $comment_id , $user_id)
     {
        $comment = Comment::find($comment_id);
        if(!$comment)
        {
            return $this->apiResponse('Not Found' , 404);
        }

       

        $userId = $comment->user->id;
        if($user_id == $userId)
        {
            $deleted_comment = Comment::with('user')->find($comment_id);
            $comment->delete();
            return $this->apiResponse('comment deleted successfully' , 200 ,new CommentResource($deleted_comment));
        }

        return $this->apiResponse('Cant delete this comment' , 400);

     }

}
