<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Like;
use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use App\Models\EmployeeWork;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\FeedbackService;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\LocationResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EmployeeProfileResource;
use App\Http\Requests\EmployeeCompletedDataRequest;
use App\Http\Requests\UpdateEmployeeProfileRequest;
use App\Http\Resources\ShowEmployeeByLocationResource;
use App\Http\Resources\ShowEmployeeBySectionAndServiceResource;

class EmployeeController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/employee/employeeCompleteData",
     *     summary="Add details to employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Description of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Nmae of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="company_image",
     *                     type="string",
     *                     format="binary",
     *                     description="صوره الشركه"
     *                 ),
     *                 @OA\Property(
     *                     property="imageSSN",
     *                     type="string",
     *                     format="binary",
     *                     description="صورة الباطاقة"
     *                 ),
     *                 @OA\Property(
     *                     property="livePhoto",
     *                     type="string",
     *                     format="binary",
     *                     description="صورة لايف"
     *                 ),
     *                 @OA\Property(
     *                     property="nationalId",
     *                     type="string",
     *                     description=" الرقم القومي"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number_2",
     *                     type="string",
     *                     description="phone_number_2"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_number_1",
     *                     type="string",
     *                     description="mobile_number_1"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_number_2",
     *                     type="string",
     *                     description="mobile_number_2"
     *                 ),
     *                 @OA\Property(
     *                     property="fax_number",
     *                     type="string",
     *                     description="fax_number"
     *                 ),
     *                 @OA\Property(
     *                     property="whatsapp_number",
     *                     type="string",
     *                     description="whatsapp_number"
     *                 ),
     *                 @OA\Property(
     *                     property="facebook_link",
     *                     type="string",
     *                     description="facebook_link"
     *                 ),
     *                  @OA\Property(
     *                     property="instagram_link",
     *                     type="string",
     *                     description="instagram_link"
     *                 ),
     *                  @OA\Property(
     *                     property="linked_in_link",
     *                     type="string",
     *                     description="linked_in_link"
     *                 ),
     *                 @OA\Property(
     *                     property="website",
     *                     type="string",
     *                     description="website"
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     description="city"
     *                 ),
     *                 @OA\Property(
     *                     property="bitTitle",
     *                     type="string",
     *                     description="bitTitle"
     *                 ),
     *                 @OA\Property(
     *                     property="street",
     *                     type="string",
     *                     description="street"
     *                 ),
     *                 @OA\Property(
     *                     property="specialMarque",
     *                     type="string",
     *                     description="specialMarque"
     *                 ),
     *                 @OA\Property(
     *                     property="zipCode",
     *                     type="string",
     *                     description=" city zip code"
     *                 ),
     *                 @OA\Property(
     *                     property="lat",
     *                     type="integer",
     *                     description="lat"
     *                 ),
     *                 @OA\Property(
     *                     property="long",
     *                     type="integer",
     *                     description="long"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID"
     *                 ),
     *                 @OA\Property(
     *                     property="service_id",
     *                     type="integer",
     *                     description="Service ID"
     *                 ),
     *                 @OA\Property(
     *                     property="section_id",
     *                     type="integer",
     *                     description="Section ID"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee details add successfully",
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
    public function employeeCompleteData(EmployeeCompletedDataRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('imageSSN')) {
                $newImageSsn = $request->file('imageSSN')->move(public_path('employees_ssn'), $request->file('imageSSN')->getClientOriginalName());
                $imageSsnUrl = asset('employees_ssn/' . $request->file('imageSSN')->getClientOriginalName());
                $validatedData['imageSSN'] = $imageSsnUrl;
            }

            if ($request->hasFile('livePhoto')) {
                $newImageLive = $request->file('livePhoto')->move(public_path('employees_live_photo'), $request->file('livePhoto')->getClientOriginalName());
                $imageLiveUrl = asset('employees_live_photo/' . $request->file('livePhoto')->getClientOriginalName());
                $validatedData['livePhoto'] = $imageLiveUrl;
            }
            if($request->hasFile('company_image'))
            {
                $image_company = $request->file('company_image')->move(public_path('company_image'), $request->file('company_image')->getClientOriginalName());
                $imageLiveUrl = asset('company_image/' . $request->file('company_image')->getClientOriginalName());
                $validatedData['company_image'] = $imageLiveUrl;
            }

            $employee = Employee::create($validatedData);
            $location = Location::create($validatedData);

            // Load related data
            $employee = $employee->load('user', 'user.locations', 'section', 'service');

            return $this->apiResponse('Details added to profile successfully', 200, new EmployeeResource($employee));
        } catch (Throwable $e) {
            return $this->apiResponse('Something went wrong', 500, $e->getMessage());
        }
    }




    /**
     * @OA\Get(
     *     path="/api/employee/employeeProfile/{employee_id}/{user_id}",
     *     summary="Show employee profile",
     *     description="Show employee profile by user id where type employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="path",
     *         description="ID of the user where user type employee to show profile",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show employee profile successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function employeeProfile($employee_id , $user_id)
    {
        try{
        // find user where type employee and company
        $user = User::where('id', $employee_id)
        ->whereIn('userType', ['employee', 'company'])
        ->first();

        $like = Like::where('user_id' , $user_id)->where('employee_id' , $employee_id)->get();
        $status = $like->isEmpty()? false : true;

        // get all employee data
        $employee = Employee::with(['user','service', 'section','user.locations','feedbacks' , 'likes','works'])->where('user_id',$user->id)->first();

        return $this->apiResponse('Employee profile fetched successfully',200,new EmployeeProfileResource($employee , $status));
        }
        catch (Throwable $e) {
            return $this->apiResponse('something error',500);
        }
    }





    /**
     * @OA\Get(
     *     path="/api/employee/section/{section_id}/service/{service_id}/{user_type}",
     *     summary="Get all employees in section and service",
     *     tags={"Employee"},
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
     *     @OA\Parameter(
     *         name="service_id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="user_type",
     *         in="path",
     *         required=true,
     *         description="Type Of User",
     *         @OA\Schema(
     *             type="string",
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
     *         description="No employees found in this service",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No employee found in this service")
     *         )
     *     )
     * )
     */

     public function showAllEmployeesBySectionIdAndServiceId($section_id, $service_id , $user_type)
     {
        try{
            $users = User::where('userType' , $user_type)->get();

            $allEmployees = collect();

            foreach($users as $user) {
                $employees = Employee::with(['works' , 'user' , 'user.locations' , 'section' , 'service' , 'likes'])
                                        ->where('service_id', $service_id)
                                        ->where('section_id', $section_id)
                                        ->where('user_id', $user->id)
                                        ->get();

                $allEmployees = $allEmployees->merge($employees);
            }

            if ($allEmployees->isEmpty()) {
                return $this->apiResponse('No employees found for this service', 404);
            }


            return $this->apiResponse(
                'Show all employees successfully',
                200,
                ShowEmployeeBySectionAndServiceResource::collection($allEmployees)
            );
        }

        catch (Throwable $e) {
            return $this->apiResponse('something error', 500, $e->getMessage());
        }
     }



    /**
     * @OA\post(
     *     path="/api/employee/showAllEmployeeBylocation/{city}",
     *     description="Show all employee by city",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         description="city to show all employees in this city",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show all employees successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function showAllEmployeeByLocation($city)
    {
        try{
            $locations = Location::whereHas('user', function($q) use ($city) {
                $q->where('city', $city)
                  ->whereIn('userType', ['employee', 'company']);
            })->with(['user.employee.service', 'user.employee.section','user.employee.likes','user.employee.feedbacks'])->get();

            // Transform the data using the ShowEmployeeByLocationResource
            $data = ShowEmployeeByLocationResource::collection($locations);

            return $this->apiResponse('Show all employees successfully', 200, $data);
        }

        catch (Throwable $e) {
            return $this->apiResponse('something error', 500, $e->getMessage());
        }

    }





}
