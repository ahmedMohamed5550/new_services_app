<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeCompletedDataRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\UpdateEmployeeProfileRequest;
use App\Http\Resources\EmployeeProfileResource;
use App\Http\Resources\EmployeeResource;
use App\Models\EmployeeWork;
use App\Services\FeedbackService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class EmployeeController extends Controller
{
    use ApiResponseTrait;

    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

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
     *                     property="desc",
     *                     type="string",
     *                     description="Description of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     type="string",
     *                     description="location of the employee"
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
     *                     property="min_price",
     *                     type="integer",
     *                     description="Minimum price"
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
        $validatedData = $request->validated();

        $imageSsnUrl = null;
        $imageLive = null;

        if ($request->hasFile('imageSSN')) {
            $newImageSsn = $request->file('imageSSN')->store('employees_ssn', 'public');
            $imageSsnUrl = Storage::url($newImageSsn);
        }

        if ($request->hasFile('livePhoto')) {
            $newImageLive = $request->file('livePhoto')->store('employees_live_photo', 'public');
            $imageLive = Storage::url($newImageLive);
        }

        $employee = Employee::create([
            'desc' => $request->desc,
            'location' => $request->location,
            'imageSSN' => $imageSsnUrl,
            'livePhoto' => $imageLive,
            'nationalId' => $request->nationalId,
            'min_price' => $request->min_price,
            'user_id' => $request->user_id,
            'service_id' => $request->service_id
        ]);

        if ($request->has('works')) {
            $works = $request->works;
            $works = array_pad($works, 4, ['image' => null]);

            foreach ($works as $work) {
                $workImageUrl = null;

                if (isset($work['image']) && $work['image']) {
                    $workImagePath = $work['image']->store('employee_works', 'public');
                    $workImageUrl = Storage::url($workImagePath);
                }

                EmployeeWork::create([
                    'user_id' => $request->user_id,
                    'image_url' => $workImageUrl,
                ]);
            }
        } else {
            for ($i = 0; $i < 4; $i++) {
                EmployeeWork::create([
                    'user_id' => $request->user_id,
                    'image_url' => null,
                ]);
            }
        }
        $employee = $employee->load('works');

        return $this->apiResponse('Details added to profile successfully',200,new EmployeeResource($employee));
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
     * @OA\Post(
     *     path="/api/employee/updateWorksImage/{user_id}",
     *     summary="Edit an employee works image",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
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
     *                     property="works[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="works[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="works[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="works[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                     nullable=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Employee updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Employee updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Employee not found")
     *         )
     *     )
     * )
     */


     public function updateWorksImage(Request $request, $user_id)
     {
         $validatedData = Validator::make($request->all(), [
             'works' => 'nullable|array|max:4',
             'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
         ]);

         if ($validatedData->fails()) {
             return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
         }

         if ($request->has('works')) {
             $works = $request->works;
             $works = array_pad($works, 4, ['image' => null]);
             $existingWorks = EmployeeWork::where('user_id', $user_id)->get();

             // Check if user exists
             if ($existingWorks->isEmpty()) {
                 return response()->json(['status' => false, 'message' => 'User not found'], 404);
             }

             $updateData = [];
             $newWorks = [];

             foreach ($works as $index => $work) {
                 $workImageUrl = null;

                 if (isset($work['image']) && $work['image']) {
                     $workImage = $work['image'];
                     $workImagePath = $workImage->store('employee_works', 'public');
                     $workImageUrl = Storage::url($workImagePath);
                 }

                 if (isset($existingWorks[$index])) {
                     $existingWork = $existingWorks[$index];
                     if ($existingWork->image_url && $workImageUrl) {
                         $oldImagePath = str_replace('/storage', 'public', parse_url($existingWork->image_url, PHP_URL_PATH));
                         Storage::delete($oldImagePath);
                     }
                     $updateData[] = [
                         'id' => $existingWork->id,
                         'image_url' => $workImageUrl ?? $existingWork->image_url,
                     ];
                 } else {
                     $newWorks[] = [
                         'user_id' => $user_id,
                         'image_url' => $workImageUrl,
                     ];
                 }
             }

             // Perform bulk update
             if (!empty($updateData)) {
                 foreach ($updateData as $data) {
                     EmployeeWork::where('id', $data['id'])->update([
                         'image_url' => $data['image_url']
                     ]);
                 }
             }

             // Perform bulk insert
             if (!empty($newWorks)) {
                 EmployeeWork::insert($newWorks);
             }

             // Delete excess works
             if (count($existingWorks) > count($works)) {
                 $excessWorks = $existingWorks->slice(count($works));
                 foreach ($excessWorks as $work) {
                     if ($work->image_url) {
                         $workImagePath = str_replace('/storage', 'public', parse_url($work->image_url, PHP_URL_PATH));
                         Storage::delete($workImagePath);
                     }
                     $work->delete();
                 }
             }
         }
         return $this->apiResponse('Edit employee works image successfully',200);
     }


    /**
     * @OA\Get(
     *     path="/api/employee/employeeProfile/{id}",
     *     summary="Show employee profile",
     *     description="Show employee profile by employee id",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the employee to show profile",
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
        $employee = Employee::findOrFail($id);

         // Optionally compute the average rating if needed
         // $averageRating = $this->feedbackService->getAverageRatingPerEmployee($id);

         // Prepare additional data if required
         // $employee->average_rating = $averageRating['average_rating'] ?? null;

        return $this->apiResponse('Employee profile fetched successfully',200,new EmployeeResource($employee));
     }



    /**
     * @OA\Post(
     *     path="/api/employee/editEmployeeProfile/{id}",
     *     summary="Update employee profile",
     *     operationId="editEmployeeProfile",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the employee to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="Description of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="min_price",
     *                     type="integer",
     *                     description="Minimum price"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile image of the employee"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Employee profile updated successfully"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Employee not found"
     *             )
     *         )
     *     )
     * )
     */


     public function editEmployeeProfile(UpdateEmployeeProfileRequest $request, $id)
     {
         $validatedData = $request->validated();

         $employee = Employee::findOrFail($id);

         // Update user data
         $user = $employee->user;

         $user->name = $validatedData['name'] ?? $user->name;

         if ($request->hasFile('image')) {
             // Delete the old image if exists
             if ($user->image) {
                 $oldImagePath = str_replace('/storage', 'public', parse_url($user->image, PHP_URL_PATH));
                 Storage::delete($oldImagePath);
             }

             // Store the new image
             $imagePath = $request->file('image')->store('users_folder', 'public');
             $user->image = Storage::url($imagePath);
         }
         $user->save();

         // Update employee data
         $employee->desc = $validatedData['desc'] ?? $employee->desc;
         $employee->min_price = $validatedData['min_price'] ?? $employee->min_price;
         $employee->save();

        return $this->apiResponse('Employee profile updated successfully',200,new EmployeeProfileResource($employee));
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
     *     path="/api/employee/showEmployeeLastWorks/{user_id}",
     *     summary="Show all employee work images",
     *     description="Show all employee work images by user ID",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user to show all last works images",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show last work successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="true"),
     *             @OA\Property(property="Employee Work Image", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No user found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No user found")
     *         )
     *     )
     * )
     */

    public function showEmployeeLastWorks($id){
        $employeeWork = EmployeeWork::where('user_id',$id)->get();
        if($employeeWork ->count() != 0){
            foreach($employeeWork as $employeeWorks){
                $employeeWorks;
            }
            return response()->json([
                'status' => 'true',
                'Employee Work Image' => $employeeWork,
            ],200);
        }

        else{
            return response()->json([
                'status' => false,
                'message' => 'no user found',
            ],401);
        }

    }





    /**
     * @OA\Get(
     *     path="/api/employee/showAllEmployeesByServiceId/{service_id}",
     *     summary="Get all employees in each service",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
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

     public function showAllEmployeesByServiceId($service_id)
     {
         $allEmployees = Employee::where('service_id', $service_id)
         ->where('checkByAdmin','accepted')
         ->get();

         if ($allEmployees->count() != 0) {
             $employeesWithRatings = $allEmployees->map(function ($employee) {
                 $employee->user->works;
                 $employee->service;
                 $averageRating = $this->feedbackService->getAverageRatingPerEmployee($employee->id);
                 $totalRates = $employee->feedbacks->count();

                 // Add average rating and total rates to the employee
                 $employee->average_rating = $averageRating['average_rating'];
                 $employee->total_rates = $totalRates;

                 // Remove the feedbacks relation to avoid including it in the response
                 unset($employee->feedbacks);

                 return $employee;
             });

             return response()->json([
                 'status' => true,
                 'allemployee' => $employeesWithRatings,
             ], 200);
         }

         return response()->json([
             'status' => false,
             'message' => 'No employees found for this service.',
         ], 404);
     }







    /**
     * @OA\Get(
     *     path="/api/employee/getTotalOrders/{id}/orders/total",
     *     summary="Get total count of orders for employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
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
     *             @OA\Property(property="total orders", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No employee found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No employee found")
     *         )
     *     )
     * )
     */

    //  public function getTotalOrders($employeeId)
    //  {
    //      $employee = Employee::find($employeeId);

    //      if (!$employee) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'no employee found',
    //         ],404);
    //      }

    //      $totalOrders = $employee->orders()->count();

    //      return response()->json([
    //         'status' => true,
    //         'total orders' => $totalOrders,
    //     ],200);
    //  }



}
