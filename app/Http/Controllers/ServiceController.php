<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    use ApiResponseTrait;


    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Show all services",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="services", type="array", @OA\Items(type="object"))
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
        $services = Service::get();
        if(count($services) > 0)
        {
            return $this->apiResponse('All services' , 200 , ServiceResource::collection($services));
        }
        return $this->apiResponse('no services' , 200 , $services);
    }


    /**
     * @OA\Get(
     *     path="/api/services/show/{service_id}",
     *     summary="Get service by id",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="service_id",
     *         in="path",
     *         required=true,
     *         description="service id",
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
        $service = Service::find($id);

        if($service)
        {
            return $this->apiResponse("show Service done" , 200 , new ServiceResource($service));
        }
        return $this->apiResponse('not found' , 404);
    }

        /**
     * @OA\Get(
     *     path="/api/services/show/section/{section_id}",
     *     summary="Get all services in section",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID of the section",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="allemployee", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No services found in this section",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No employee found in this service")
     *         )
     *     )
     * )
     */

    public function showAllServicesBySection($section_id){
        $allServices = Service::where('section_id', $section_id)->get();

        if ($allServices->isEmpty()) {
            return $this->apiResponse('No services found for this section', 404);
        }

        $allServices->load('section');

        return $this->apiResponse(
            'Show all services successfully',
            200,
            ServiceResource::collection($allServices)
        );
    }


    /**
     * @OA\Post(
     *     path="/api/services/create",
     *     summary="create a new service",
     *     tags={"services"},
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
     *                     description="service description"
     *                 ),
     *                 @OA\Property(
     *
     *                     property="section_id",
     *                     type="integer",
     *                     description="section id"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="service image"
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
     *         description="add new service successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */

    //create new service
    public function store(ServiceRequest $request)
    {
        $validatedData = $request->validated();

        $image_path = null;

        if($request->hasFile('image'))
        {
            $image_path = $request->file('image')->store('services_image' , 'uploads');
        }

        $validatedData['image'] = $image_path;

        $service = Service::create($validatedData);
        return $this->apiResponse('service created successfully' , 200 , new ServiceResource($service));
    }

    /**
     * @OA\Post(
     *     path="/api/services/edit/{service_id}",
     *     summary="edit to services",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="service_id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
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
     *                     description="service name"
     *                 ),
    *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="service description"
     *                 ),
    *                 @OA\Property(
     *                     property="section_id",
     *                     type="integer",
     *                     description="section id"
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
     *         description="update service successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */

    //update service
    public function update(ServiceRequest $request , $id)
    {
        $service = Service::find($id);

        if(!$service)
        {
            return $this->failed('not found' , 404);
        }

        $old_image = $service->image;

        $validatedData = $request->validated();

        $new_image = null;

        if($request->hasFile('image'))
        {
            $new_image = $request->file('image')->store('services_image' , 'uploads');
        }
        if($new_image != null)
        {
            $validatedData['image'] = $new_image;
        }

        $service->update($validatedData);

        if($new_image != null && isset($old_image))
        {
            Storage::disk('uploads')->delete($old_image);
        }

        return $this->apiResponse('service updated Successfully' , 200 , new ServiceResource($service));

    }



    /**
    * @OA\Delete(
    *     path="/api/services/delete/{service_id}",
    *     summary="Delete an service",
    *     description="Delete service by ID",
    *     tags={"services"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="service_id",
    *         in="path",
    *         description="ID of the service to delete",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="service deleted successfully"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="service not found"
    *     )
    * )
    */

    //delete service
    public function delete($id)
    {
        $service = Service::find($id);

        if(!$service)
        {
            return $this->failed('not found' , 404);
        }

        $image = $service->image;

        $service->delete();

        if($image != null)
        {
            Storage::disk('uploads')->delete($image);
        }

        return $this->apiResponse('service deleted successfully' , 200 , $service);


    }

}
