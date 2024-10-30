<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\SectionResource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    use ApiResponseTrait;


    /**
     * @OA\Get(
     *     path="/api/sections",
     *     summary="Show all sections",
     *     tags={"sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="sections", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    //all services
    public function index()
    {
        $sections = Section::with('services')->get();
        if(count($sections) > 0)
        {
            return $this->apiResponse('All sections' , 200 , SectionResource::collection($sections));
        }
        return $this->apiResponse('no sections' , 200 , $sections);
    }


     /**
     * @OA\Get(
     *     path="/api/sections/show/{section_id}",
     *     summary="Get section by id",
     *     tags={"sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="section id",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *     ),
     *
     * )
     */

    //one service
    public function show($id)
    {
        $section = Section::with('services')->find($id);

        if($section)
        {
            return $this->apiResponse("show section done" , 200 , new SectionResource($section));
        }
        return $this->apiResponse('not found' , 404);
    }



    /**
     * @OA\Post(
     *     path="/api/sections/create",
     *     summary="create a new section",
     *     tags={"sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="name of service"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="section description"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="section image"
     *                 ),
    *     @OA\Property(
    *         property="comment",
    *         type="string",
    *         description="to access image use https://mahllola.online/public/image  example : https://mahllola.online/public/storage/services_folder/ttyVNuauz67kqXX40jyewMwh3DWpdFjjyJ0pIiPd.jpg"
    *     )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="add new section successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */


     public function store(SectionRequest $request)
     {
         $validatedData = $request->validated();

         $image_path = null;

         if ($request->hasFile('image')) {
             $image_path = $request->file('image')->move(public_path('sections_image'), $request->file('image')->getClientOriginalName());
             $image_path = asset('sections_image/' . $request->file('image')->getClientOriginalName());
         }

         $validatedData['image'] = $image_path;

         $section = Section::create($validatedData);

         return $this->apiResponse('section created successfully', 200, new SectionResource($section));
     }



     /**
     * @OA\Post(
     *     path="/api/sections/edit/{section_id}",
     *     summary="edit to sections",
     *     tags={"sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID of the section",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="section name"
     *                 ),
    *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="section description"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="update section successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $section = Section::find($id);

        if (!$section) {
            return $this->failed('Section not found', 404);
        }

        $validatedData = $request->all();
        $new_image = null;

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($section->image) {
                $old_image_path = public_path('sections_image/' . basename($section->image));

                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }

            // Save the new image to the public directory
            $new_image = $request->file('image')->move(public_path('sections_image'), $request->file('image')->getClientOriginalName());
            $new_image = asset('sections_image/' . $request->file('image')->getClientOriginalName());
        }

        // If a new image was uploaded, update the image path
        if ($new_image) {
            $validatedData['image'] = $new_image;
        }

        $section->update($validatedData);

        return $this->apiResponse('Section updated successfully', 200, new SectionResource($section));
    }




    /**
    * @OA\Delete(
    *     path="/api/sections/delete/{section_id}",
    *     summary="Delete an sections",
    *     description="Delete section by ID",
    *     tags={"sections"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="section_id",
    *         in="path",
    *         description="ID of the service to delete",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="section deleted successfully"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="section not found"
    *     )
    * )
    */


    public function delete($id)
    {
        $section = Section::find($id);

        if (!$section) {
            return $this->failed('Section not found', 404);
        }

        $image = $section->image;

        $section->delete();

        if ($image) {
            $image_path = public_path('sections_image/' . basename($image));

            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        return $this->apiResponse('Section deleted successfully', 200, null);
    }




}
