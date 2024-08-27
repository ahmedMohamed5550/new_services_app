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
