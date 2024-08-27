<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;


// Route::post('refresh', [AuthController::class,'refresh']);
// Route::post('logout', [AuthController::class,'logout']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::post('register',[UserController::class,'register']);
Route::post('login', [LoginController::class,'login']);

Route::prefix('services')->group(function(){
    Route::post('/', [ServiceController::class, 'store']);
    Route::get('/', [ServiceController::class, 'show']);
    Route::get('/{id}' , [ServiceController::class , 'showOne']);
    Route::post('/{id}' , [ServiceController::class , 'update']);
    Route::delete('/{id}' , [ServiceController::class , 'delete']);
});

Route::prefix('employees')->group(function(){
    Route::post('/' , [EmployeeController::class , 'store']);
    Route::get('/{id}' , [EmployeeController::class , 'showOne']);
});



