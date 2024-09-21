<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Employee;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/Admin/changeEmployeeStatus/{id}",
     *     summary="Change employee status between ['available', 'busy']",
     *     tags={"Admin"},
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
     *     path="/api/Admin/changeCheckByAdmin/{id}",
     *     summary="Change employee status between ['accepted','rejected']",
     *     tags={"Admin"},
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
}
