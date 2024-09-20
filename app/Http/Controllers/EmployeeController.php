<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Employee;
use App\Models\Location;
use App\Models\EmployeeWork;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\FeedbackService;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\LocationResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EmployeeProfileResource;
use App\Http\Requests\EmployeeCompletedDataRequest;
use App\Http\Requests\UpdateEmployeeProfileRequest;
use App\Http\Resources\ShowEmployeeByLocationResource;
use App\Models\User;

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
     *                     property="type",
     *                     type="string",
     *                     description="choose between ['company','individual']"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Description of the employee"
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
        try{
            $validatedData = $request->validated();

            // Check if the request has an imageSSN file
            if ($request->hasFile('imageSSN')) {
                $newImageSsn = $request->file('imageSSN')->store('employees_ssn', 'public');
                $imageSsnUrl = Storage::url($newImageSsn);
                // Add the imageSSN URL to the validated data
                $validatedData['imageSSN'] = $imageSsnUrl;
            }

            // Check if the request has a livePhoto file
            if ($request->hasFile('livePhoto')) {
                $newImageLive = $request->file('livePhoto')->store('employees_live_photo', 'public');
                $imageLiveUrl = Storage::url($newImageLive);
                // Add the live photo URL to the validated data
                $validatedData['livePhoto'] = $imageLiveUrl;
            }


            $employee = Employee::create($validatedData);
            $location = Location::create($validatedData);

            $employee = $employee->load('user','user.locations','section','service');

            return $this->apiResponse('Details added to profile successfully',200,new EmployeeResource($employee));
        }

        catch (Throwable $e) {
            return $this->apiResponse('something error', 500, $e->getMessage());
        }

    }



    /**
     * @OA\Post(
     *     path="/api/employee/updateEmployeeCompleteData/{id}",
     *     summary="Update details of an employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee to update data",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="Description of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     type="string",
     *                     description="location in details"
     *                 ),
     *                 @OA\Property(
     *                     property="imageSSN",
     *                     type="string",
     *                     format="binary",
     *                     description="Image of the SSN"
     *                 ),
     *                 @OA\Property(
     *                     property="livePhoto",
     *                     type="string",
     *                     format="binary",
     *                     description="Live photo of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="nationalId",
     *                     type="string",
     *                     description="National ID of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="min_price",
     *                     type="integer",
     *                     description="Minimum price of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="service_id",
     *                     type="integer",
     *                     description="Service ID of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="works[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                      nullable=true,
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee details updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Employee profile details updated successfully"
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



     public function updateEmployeeCompleteData(Request $request, $employeeId)
     {
         $validatedData = Validator::make($request->all(), [
             'desc' => 'required|string',
             'location' => 'required|string',
             'imageSSN' => 'file|mimes:jpeg,png,jpg,gif',
             'livePhoto' => 'file|mimes:jpeg,png,jpg,gif',
             'nationalId' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:13',
             'min_price' => 'required',
             'user_id' => 'required|exists:users,id',
             'service_id' => 'required|exists:services,id',
             'works' => 'nullable|array|max:4',
             'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
         ]);

         if ($validatedData->fails()) {
             return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
         }

         $employee = Employee::where('id', $employeeId)
         ->where('checkByAdmin', 'rejected')
         ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'can‘t update data',
            ], 404);
        }

         // Update existing fields
         $employee->desc = $request->desc;
         $employee->location = $request->location;
         $employee->nationalId = $request->nationalId;
         $employee->min_price = $request->min_price;
         $employee->user_id = $request->user_id;
         $employee->service_id = $request->service_id;
         $employee->checkByAdmin = 'waiting';

         // Handle imageSSN update
         if ($request->hasFile('imageSSN')) {
             // Delete old image if exists
             if ($employee->imageSSN) {
                 Storage::delete(str_replace('/storage', 'public', $employee->imageSSN));
             }

             $newImageSsn = $request->file('imageSSN')->store('employees_ssn', 'public');
             $employee->imageSSN = Storage::url($newImageSsn);
         }

         // Handle livePhoto update
         if ($request->hasFile('livePhoto')) {
             // Delete old image if exists
             if ($employee->livePhoto) {
                 Storage::delete(str_replace('/storage', 'public', $employee->livePhoto));
             }

             $newImageLive = $request->file('livePhoto')->store('employees_live_photo', 'public');
             $employee->livePhoto = Storage::url($newImageLive);
         }

         // Save updated employee data
         $employee->save();

         // Update works if provided
         if ($request->has('works')) {
             $works = $request->works;

             foreach ($works as $index => $work) {
                 $workImageUrl = null;

                 if (isset($work['image']) && $work['image']) {
                     // Delete old image if exists
                     $existingWork = EmployeeWork::where('user_id', $employee->user_id)
                         ->orderBy('id', 'asc')
                         ->skip($index)
                         ->first();

                     if ($existingWork && $existingWork->image_url) {
                         Storage::delete(str_replace('/storage', 'public', $existingWork->image_url));
                     }

                     $workImage = $work['image'];
                     $workImagePath = $workImage->store('employee_works', 'public');
                     $workImageUrl = Storage::url($workImagePath);
                 }

                 // Find existing or create new EmployeeWork
                 $employeeWork = EmployeeWork::where('user_id', $employee->user_id)
                     ->orderBy('id', 'asc')
                     ->skip($index)
                     ->first();

                 if ($employeeWork) {
                     // Update existing EmployeeWork
                     $employeeWork->image_url = $workImageUrl;
                     $employeeWork->save();
                 } else {
                     // Create new EmployeeWork
                     EmployeeWork::create([
                         'user_id' => $employee->user_id,
                         'image_url' => $workImageUrl,
                     ]);
                 }
             }
         }

         return response()->json([
             'status' => true,
             'message' => 'Employee profile details updated successfully',
             'employee' => $employee,
         ], 200);
     }





    /**
     * @OA\Get(
     *     path="/api/employee/employeeProfile/{id}",
     *     summary="Show employee profile",
     *     description="Show employee profile by user id where type employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user where user type employee to show profile",
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

    public function employeeProfile($id)
    {
        try{
        // find user where type employee
        $user = User::where('id',$id)->where('userType','employee')->first();

        // get all employee data
        $employee = Employee::with(['user','service', 'section','user.locations','feedbacks' , 'likes'])->where('user_id',$user->id)->first();

        return $this->apiResponse('Employee profile fetched successfully',200,new EmployeeProfileResource($employee));
        }
        catch (Throwable $e) {
            return $this->apiResponse('something error',500);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/employee/changeEmployeeStatus/{id}",
     *     summary="Change employee status between ['available', 'busy']",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
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
     *                     property="status",
     *                     type="string",
     *                     description="Employee status",
     *                     example="available"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Change employee status successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="true"),
     *             @OA\Property(property="message", type="string", example="Change status successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="No employee found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="No employee found")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */


    // function to change employee status

    public function changeEmployeeStatus(Request $request,$id){
        try{
        $employee=Employee::find($id);
            if($employee){
                $employee->update(['status'=> $request->status]);

                return $this->apiResponse('change status successfully',200);
            }

            else {
                return $this->apiResponse('no employee found',404);
            }

        }

        catch (Throwable $e) {
            throw $e;
        }
    }



    /**
     * @OA\Post(
     *     path="/api/employee/changeCheckByAdmin/{id}",
     *     summary="Change employee status between ['accepted','rejected']",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
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
     *                     property="checkByAdmin",
     *                     type="string",
     *                     description="checkByAdmin to show Employee data",
     *                     example="accepted"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Change employee status successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="true"),
     *             @OA\Property(property="message", type="string", example="Change status successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="No employee found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="No employee found")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */


        // function to change employee status

        public function changeCheckByAdmin(Request $request,$id){
            try{
            $employee=Employee::find($id);
                if($employee){
                    $employee->update(['checkByAdmin'=> $request->checkByAdmin]);

                    return $this->apiResponse('change checkByAdmin successfully',200);
                }

                else {
                    return $this->apiResponse('no employee found',404);
                }

            }

            catch (Throwable $e) {
                throw $e;
            }
        }





    /**
     * @OA\Get(
     *     path="/api/employee/section/{section_id}/service/{service_id}",
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

     public function showAllEmployeesBySectionIdAndServiceId($section_id, $service_id)
     {
        try{
            $allEmployees = Employee::where('service_id', $service_id)
            ->where('section_id', $section_id)
            ->get();

            if ($allEmployees->isEmpty()) {
                return $this->apiResponse('No employees found for this service', 404);
            }

            $allEmployees->load('works', 'user', 'user.locations', 'section', 'service' , 'likes');

            return $this->apiResponse(
                'Show all employees successfully',
                200,
                EmployeeResource::collection($allEmployees)
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
                  ->where('userType', 'employee');
            })->with(['user.employee.service', 'user.employee.section'])->get();

            // Transform the data using the ShowEmployeeByLocationResource
            $data = ShowEmployeeByLocationResource::collection($locations);

            return $this->apiResponse('Show all employees successfully', 200, $data);
        }

        catch (Throwable $e) {
            return $this->apiResponse('something error', 500, $e->getMessage());
        }

     }



}
