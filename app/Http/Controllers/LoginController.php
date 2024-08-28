<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Authenticate user and generate token",
     * tags={"userAuth"},
     * @OA\Parameter(
     *     name="email_or_phone",
     *     in="query",
     *     description="User's email or phone to login",
     *     required=true,
     *     @OA\Schema(
     *         type="string",
     *         example="ahmed@gmail.com"
     *     )
     * ),
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     description="User's password",
     *     required=true,
     *     @OA\Schema(
     *         type="string",
     *         example="Am123456"
     *     )
     * ),
     * @OA\Response(
     *     response="200",
     *     description="Login successful"
     * ),
     * @OA\Response(
     *     response="401",
     *     description="Invalid credentials"
     * )
     * )
     */

    public function login(LoginRequest $request)
    {
        $loginData = $request->validated();

        $fieldType = filter_var($loginData['email_or_phone'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!$token = Auth::attempt([$fieldType => $loginData['email_or_phone'], 'password' => $loginData['password']])) {
            return $this->apiResponse('Unauthorized. Incorrect email or password.', 401);
        }

        $user = Auth::user();

        return $this->apiResponse('User login successfully', 200, new LoginResource($user, $token));
    }


}
