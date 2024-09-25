<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditUserProfileRequest;
use App\Http\Resources\UpdateUserProfileResource;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"userAuth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User's name"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User's email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User's password"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="User's phone"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="User's image"
     *                 ),
     *                 @OA\Property(
     *                     property="userType",
     *                     type="string",
     *                     description="userType choose between ['user','employee','company','admin']"
     *                 ),
    *     @OA\Property(
    *         property="comment",
    *         type="string",
    *         description="to access image use https://mahllola.online/public/image  example : https://mahllola.online/public/image_path image_path restore when added image"
    *     ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */

     public function register(UserRequest $request)
     {
         $validatedData = $request->validated();
         $image_path = null;

         if ($request->hasFile('image')) {
             // Move the uploaded image to the public directory
             $image_path = $request->file('image')->move(public_path('users_folder'), $request->file('image')->getClientOriginalName());
             // Generate a public URL for the image
             $image_path = asset('users_folder/' . $request->file('image')->getClientOriginalName());
         }

         $validatedData['image'] = $image_path;

         $validatedData['password'] = Hash::make($validatedData['password']);

         $user = User::create($validatedData);

         return $this->apiResponse('User registered successfully', 200, new UserResource($user));
     }


    /**
     * @OA\Post(
     *     path="/api/user/editUserProfile/{user_id}",
     *     summary="Edit user profile",
     *     tags={"userAuth"},
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
     *                     property="name",
     *                     type="string",
     *                     description="Name"
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
     *         description="Update profile successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid input data"),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized action")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

     public function editUserProfile(EditUserProfileRequest $request, $id)
     {
         try {
             $user = User::findOrFail($id);
             $imageUrl = $user->image;

             if ($request->hasFile('image')) {
                 if ($imageUrl) {
                     $oldImagePath = public_path('users_folder/' . basename($imageUrl));

                     if (file_exists($oldImagePath)) {
                         unlink($oldImagePath);
                     }
                 }

                 $newImage = $request->file('image')->move(public_path('users_folder'), $request->file('image')->getClientOriginalName());
                 $imageUrl = asset('users_folder/' . $request->file('image')->getClientOriginalName());
             }

             $user->update([
                 'name' => $request->name,
                 'image' => $imageUrl,
             ]);

             return $this->apiResponse('User profile updated successfully', 200, new UpdateUserProfileResource($user));

         } catch (Throwable $e) {
             return $this->apiResponse('Something went wrong', 500);
         }
     }



    /**
     * @OA\Get(
     * path="/api/user",
     * summary="Get logged-in user details",
     * tags={"userAuth"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response="200", description="Success"),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function getUserDetails(Request $request)
    {
        $user = $request->user();
        return response()->json(['user' => $user], 200);
    }


    // logout function

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="User logout",
     *     tags={"userAuth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    public function logout()
    {
        Auth::logout();
        return $this->apiResponse('Successfully logged out', 200);
    }
}
