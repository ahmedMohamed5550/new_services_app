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


    //all services
    public function show()
    {
        $services = Service::get();
        if(count($services) > 0)
        {
            return $this->apiResponse('All services' , 200 , ServiceResource::collection($services));
        }
        return $this->apiResponse('no services' , 200 , $services);
    }


    //one service
    public function showOne($id)
    {
        $service = Service::find($id);

        if($service)
        {
            return $this->apiResponse("One Service" , 200 , new ServiceResource($service));
        }
        return $this->failed('not found' , 404);
    }



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
