<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;

// Route::post('refresh', [AuthController::class,'refresh']);
// Route::post('logout', [AuthController::class,'logout']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::post('/register',[UserController::class,'register']);
Route::post('/login', [LoginController::class,'login']);

Route::controller(UserController::class)->group(function(){
    Route::Post('/user/editUserProfile/{id}','editUserProfile');
    Route::get('/allUser','allUser');
    Route::get('/logout',"logout");
});

Route::group(['prefix'=>'location'],function($router){
    Route::controller(LocationController::class)->group(function(){
        Route::delete('destroy/{id}', 'destroy');
        Route::post('store', 'store');
        Route::post('update/{id}', 'update');
        Route::get('showUsersLocation/{id}', 'showUsersLocation');
    });
});

Route::prefix('services')->group(function(){
    Route::post('/create', [ServiceController::class, 'store']);
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/show/{id}' , [ServiceController::class , 'show']);
    Route::post('/edit/{id}' , [ServiceController::class , 'update']);
    Route::delete('/delete/{id}' , [ServiceController::class , 'delete']);
});


Route::prefix('sections')->group(function(){
    Route::post('/create', [SectionController::class, 'store']);
    Route::get('/', [SectionController::class, 'index']);
    Route::get('/show/{id}' , [SectionController::class , 'show']);
    Route::post('/edit/{id}' , [SectionController::class , 'update']);
    Route::delete('/delete/{id}' , [SectionController::class , 'delete']);
});

Route::controller(EmployeeController::class)->prefix('employee')->group(function(){
    Route::post('/employeeCompleteData','employeeCompleteData');
    Route::Post("/updateEmployeeCompleteData/{id}",'updateEmployeeCompleteData');
    Route::Post("/updateWorksImage/{id}",'updateWorksImage');
    Route::get('/showAllEmployeesByServiceId/{service_id}','showAllEmployeesByServiceId');
    Route::get("/employeeProfile/{id}",'employeeProfile');
    Route::get("/getTotalOrders/{id}/orders/total",'getTotalOrders');
    Route::Post("/editEmployeeProfile/{id}",'editEmployeeProfile');
    Route::get("/showEmployeeLastWorks/{id}",'showEmployeeLastWorks');
    Route::post('/changeEmployeeStatus/{id}', 'changeEmployeeStatus');
    Route::post('/changeCheckByAdmin/{id}', 'changeCheckByAdmin');
});



