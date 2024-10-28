<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavouriteRequest;
use App\Http\Resources\FavouriteResource;
use App\Http\Resources\ShowFavouriteResource;
use App\Models\Favourite;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    use ApiResponseTrait;

        /**
     * @OA\Post(
     *     path="/api/save/create",
     *     summary="Add new save profile to employee Or remove save",
     *     tags={"save"},
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
     *     @OA\Response(response="201", description="save added successfully"),
     *     @OA\Response(response="401", description="Validation errors", @OA\JsonContent())
     * )
     */

    public function store(FavouriteRequest $request)
    {
        $validatedData = $request->validated();

        $favourite = Favourite::where('user_id' , $validatedData['user_id'])->where('employee_id' , $validatedData['employee_id'])->get();

        if(count($favourite) > 0)
        {
            Favourite::where('user_id' , $validatedData['user_id'])->where('employee_id' , $validatedData['employee_id'])->delete();
            return $this->apiResponse('Saved profile deleted successfully', 200, $favourite);
        }

        $favourite = Favourite::create($validatedData);
        $favourite->load('user','employee');

        if ($favourite) {
            return $this->apiResponse('Saved profile Created successfully', 200, new FavouriteResource($favourite));
        } else {
            return $this->apiResponse('Something went wrong', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/save/show/{id}",
     *     summary="Show saved for user",
     *     description="Show saved for user by user id",
     *     tags={"save"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to show saved employee profile",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show employee profile saved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No saved found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function show($id){
        $favourites = Favourite::where('user_id' , $id)->get();

        $favourites->load('employee');

        if (count($favourites) > 0) {
            return $this->apiResponse('show all profiles to user saved successfully', 200, ShowFavouriteResource::collection($favourites));
        }

        return $this->apiResponse('No saved found', 404);
    }
}
