<?php

namespace App\Http\Controllers;

use App\Models\ImageCompany;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;

class ImageCompanyController extends Controller
{

    use ApiResponseTrait;

   /**
 * @OA\Post(
 *     path="/api/images/create",
 *     summary="Create images",
 *     tags={"images"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="image_path",
 *                     type="array",
 *                     description="Array of company images",
 *                     @OA\Items(
 *                         type="string",
 *                         format="binary"
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="employee_id",
 *                     type="integer",
 *                     description="The ID of the employee"
 *                 ),
 *                @OA\Property(
    *         property="comment",
    *         type="string",
    *         description="to access image use https://mahllola.online/public/image  example : https://mahllola.online/public/storage/services_folder/ttyVNuauz67kqXX40jyewMwh3DWpdFjjyJ0pIiPd.jpg"
    *     )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Images created successfully"
 *     ),
 *     @OA\Response(
 *         response="422",
 *         description="Validation errors"
 *     )
 * )
 */



 public function store(ImageRequest $request)
 {
     $validatedData = $request->validated();

     $company_images = [];

     foreach ($request->file('image_path') as $image) {
         $path = $image->store('company_images', 'uploads');

         $imageRecord = ImageCompany::create([
             'image_path' => $path,
             'employee_id' => $validatedData['employee_id'],
         ]);

         $company_images[] = $imageRecord;
     }

     return $this->apiResponse('Images created successfully', 201, ImageResource::collection($company_images));
 }


}
