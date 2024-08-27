<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function register(UserRequest $request)
    {
        $validatedData = $request->validated();

        $image_path = null;

        if ($request->hasFile('image')) {

            $image_path = $request->file('image')->store('users_folder', 'uploads');

        }

        $validatedData['image'] = $image_path;

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return $this->apiResponse('User registered successfully', 200, new UserResource($user));
    }
}
