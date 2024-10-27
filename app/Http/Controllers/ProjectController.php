<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectLikesResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

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

        $project->load(['service', 'user', 'section']);

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
}
