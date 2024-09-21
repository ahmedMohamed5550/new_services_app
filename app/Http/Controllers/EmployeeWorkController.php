<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeWorkResource;
use App\Models\EmployeeWork;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class EmployeeWorkController extends Controller
{
    use ApiResponseTrait;
    /**
     * @OA\Post(
     *     path="/api/work/create",
     *     summary="Add works to employee",
     *     tags={"Works"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="integer",
     *                     description="User ID where type is employee"
     *                 ),
     *                 @OA\Property(
     *                     property="images[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[4][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 5",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[0][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 1",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[1][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 2",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[2][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 3",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[3][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 4",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[4][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 5",
     *                     nullable=true,
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee works added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="successfully"
     *             ),
     *             @OA\Property(
     *                 property="employee",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */

     public function store(Request $request)
     {
         $validatedData = Validator::make($request->all(), [
             'employee_id' => 'required|exists:users,id',
             'images' => 'nullable|array|max:5',
             'images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
             'videos' => 'nullable|array|max:5',
             'videos.*.video' => 'nullable|mimes:mp4,mov,avi|max:10000', // Allow small video files up to 10MB
         ]);

         if ($validatedData->fails()) {
             return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
         }

         $createdWorks = [];

         $images = $request->has('images') ? $request->images : [];
         $videos = $request->has('videos') ? $request->videos : [];

         // Ensure both images and videos arrays have exactly 5 entries, filling with null values if necessary
         $images = array_pad($images, 5, ['image' => null]);
         $videos = array_pad($videos, 5, ['video' => null]);

         for ($i = 0; $i < 5; $i++) {
             $workImageUrl = null;
             $workVideoUrl = null;

             if (isset($images[$i]['image']) && $images[$i]['image']) {
                 $workImage = $images[$i]['image'];
                 $workImagePath = $workImage->store('employee_works/images', 'public');
                 $workImageUrl = Storage::url($workImagePath);
             }

             if (isset($videos[$i]['video']) && $videos[$i]['video']) {
                 $workVideo = $videos[$i]['video'];
                 $workVideoPath = $workVideo->store('employee_works/videos', 'public'); // Store videos in a separate directory
                 $workVideoUrl = Storage::url($workVideoPath);
             }

             $data = EmployeeWork::create([
                 'employee_id' => $request->employee_id,
                 'image_url' => $workImageUrl,
                 'video_url' => $workVideoUrl,
             ]);

             $createdWorks[] = $data;
         }

         return $this->apiResponse('Employee images and videos added successfully', 200, EmployeeWorkResource::collection($createdWorks));
     }

}
