<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use App\Services\FeedbackService;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Throw_;
use Throwable;

class FeedbackController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/feedback/create",
     *     summary="Add new feedback to employee",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="comment",
     *                      type="string",
     *                     description="Feedback comment",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer",
     *                     description="Rating (1-5)"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID where type user"
     *                 ),
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="integer",
     *                     description="user ID where type employee"
     *                 ),
     *                 required={"rating", "user_id", "employee_id"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Feedback added successfully"),
     *     @OA\Response(response="401", description="Validation errors", @OA\JsonContent())
     * )
     */
    public function store(StoreFeedbackRequest $request)
    {
        $validatedData = $request->validated();

        $feedback = Feedback::create($validatedData);
        $feedback->load('user','employee');

        if ($feedback) {
            return $this->apiResponse('Feedback submitted successfully', 200, new FeedbackResource($feedback));
        } else {
            return $this->apiResponse('Something went wrong', 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/feedback/getEmployeeFeedback/{user_id}",
     *     summary="Get all feedback for an employee",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="user ID where type emoloyee",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No feedback found for this employee",
     *         @OA\JsonContent()
     *     ),
     * )
     */

    //  function to get all feedback by employee_id and retrieve all data

    public function getEmployeeFeedback($id)
    {
        try {
            $feedback = Feedback::where('employee_id', $id)->get();

            $feedback->load('user', 'employee');

            if ($feedback->isNotEmpty()) {
                return $this->apiResponse(
                    'Feedback retrieved successfully',200,FeedbackResource::collection($feedback)
                );
            } else {
                return $this->apiResponse('No feedback found for this employee', 404);
            }
        } catch (\Throwable $e) {
            return $this->apiResponse('Something went wrong', 500, $e->getMessage());
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/feedback/delete/{id}",
     *     summary="Delete a feedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the feedback to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback deleted successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No feedback found",
     *         @OA\JsonContent()
     *     )
     * )
     */

    public function delete($id)
    {
        $feedback = Feedback::find($id);
        if ($feedback) {
            $feedback->delete();
            return $this->apiResponse('feedback deleted successfully', 200);
        }
        else {
            return $this->apiResponse('No feedback found for this employee', 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/feedback/edit/{id}",
     *     summary="Edit a feedback",
     *     operationId="editFeedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the feedback",
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
     *                     property="comment",
     *                     type="string",
     *                     description="Comment of the feedback"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer",
     *                     description="Rating (1-5)"
     *                 ),
     *                 required={"comment", "rating"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Feedback updated successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Validation errors",
     *         @OA\JsonContent()
     *     ),
     * )
     */




    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'string',
                'rating' => 'in:1,2,3,4,5',
            ]);

            // Return message failed if validation fails
            if ($validator->fails()) {
                return $this->apiResponse('validation error', 401,$validator->errors());
            }

            // Find the feedback by ID
            $feedback = Feedback::find($id);

            // Check if feedback exists
            if (!$feedback) {
                return $this->apiResponse('No feedback found for this employee', 404);
            }

            // Update comment if provided in request
            if ($request->has('comment')) {
                $feedback->comment = $request->input('comment');
            }

            // Update rating if provided in request
            if ($request->has('rating')) {
                $feedback->rating = $request->input('rating');
            }



            // Save the changes to the feedback
            $feedback->save();
            $feedback->load('user','employee');

            return $this->apiResponse('Feedback updated successfully',200,new FeedbackResource($feedback));

        } catch (Throwable $e) {
            return $this->apiResponse('Something went wrong', 500, $e->getMessage());
        }
    }
}
