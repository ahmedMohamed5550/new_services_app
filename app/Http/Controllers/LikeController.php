<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\LikeRequest;
use App\Http\Resources\LikeResource;

class LikeController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/like/create",
     *     summary="Add new like to employee Or Delete Like",
     *     tags={"like"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *
     *
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID where type user"
     *                 ),
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="integer",
     *                     description="user ID where type employee"
     *                 ),
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="like added successfully"),
     *     @OA\Response(response="401", description="Validation errors", @OA\JsonContent())
     * )
     */
    public function store(LikeRequest $request)
    {
        $validatedData = $request->validated();
        $user_id = $request->user_id;
        $employee_id = $request->employee_id;
        $like = Like::where('user_id' , $user_id)->where('employee_id' , $employee_id)->get();

        if(count($like) > 0)
        {
            Like::where('user_id' , $user_id)->where('employee_id' , $employee_id)->delete();
            return $this->apiResponse('like deleted successfully', 200, $like);
        }

        $like = Like::create($validatedData);
        $like->load('user','employee');

        if ($like) {
            return $this->apiResponse('Like Created successfully', 200, new LikeResource($like));
        } else {
            return $this->apiResponse('Something went wrong', 500);
        }
    }

    

}
