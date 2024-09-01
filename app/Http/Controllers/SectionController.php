<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\SectionResource;

class SectionController extends Controller
{
    use ApiResponseTrait;


    /**
     * @OA\Get(
     *     path="/api/sections",
     *     summary="Show all services",
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
        $sections = Section::get();
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
        $section = Section::find($id);

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

        if($request->hasFile('image'))
        {
            $image_path = $request->file('image')->store('sections_image' , 'uploads');
        }

        $validatedData['image'] = $image_path;

        $section = Section::create($validatedData);
        return $this->apiResponse('section created successfully' , 200 , new SectionResource($section));
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

    //update service
    public function update(SectionRequest $request , $id)
    {
        $section = Section::find($id);

        if(!$section)
        {
            return $this->failed('not found' , 404);
        }

        $old_image = $section->image;

        $validatedData = $request->validated();

        $new_image = null;

        if($request->hasFile('image'))
        {
            $new_image = $request->file('image')->store('sections_image' , 'uploads');
        }
        if($new_image != null)
        {
            $validatedData['image'] = $new_image;
        }

        $section->update($validatedData);

        if($new_image != null && isset($old_image))
        {
            Storage::disk('uploads')->delete($old_image);
        }

        return $this->apiResponse('section updated Successfully' , 200 , new SectionResource($section));

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

        if(!$section)
        {
            return $this->failed('not found' , 404);
        }

        $image = $section->image;

        $section->delete();

        if($image != null)
        {
            Storage::disk('uploads')->delete($image);
        }

        return $this->apiResponse('section deleted successfully' , 200 , $section);


    }



}
