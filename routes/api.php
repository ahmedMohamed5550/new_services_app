<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeWorkController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ImageCompanyController;

// Route::post('refresh', [AuthController::class,'refresh']);
// Route::post('logout', [AuthController::class,'logout']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::post('/register',[UserController::class,'register']);
Route::post('/login', [LoginController::class,'login']);

Route::controller(UserController::class)->group(function(){
    Route::Post('/user/editUserProfile/{id}','editUserProfile');
    Route::get('/user/{user_id}','userProfile');
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
    Route::get('/show/section/{section_id}' , [ServiceController::class , 'showAllServicesBySection']);
    Route::post('/edit/{id}' , [ServiceController::class , 'update']);
    Route::delete('/delete/{id}' , [ServiceController::class , 'delete']);
});

Route::prefix('like')->group(function(){

Route::post('/create' , [LikeController::class , 'store']);
Route::get('/show/{id}' , [LikeController::class , 'showLikes']);

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
    Route::get('/section/{section_id}/service/{service_id}/{user_type}','showAllEmployeesBySectionIdAndServiceId');
    Route::get("/employeeProfile/{employee_id}/{user_id}",'employeeProfile');
    Route::post("/showAllEmployeeBylocation/{city}",'showAllEmployeeBylocation');
    Route::post('/changeEmployeeStatus/{id}', 'changeEmployeeStatus');
    Route::post('/changeCheckByAdmin/{id}', 'changeCheckByAdmin');
});

Route::controller(FeedbackController::class)->prefix('feedback')->group(function () {
    Route::post('/create','store');
    Route::get('/getEmployeeFeedback/{id}', 'getEmployeeFeedback');
    Route::delete('/delete/{id}', 'delete');
    Route::post('/edit/{id}', 'update');
});

Route::controller(AdminController::class)->prefix('Admin')->group(function(){
    Route::post('/changeEmployeeStatus/{id}', 'changeEmployeeStatus');
    Route::post('/changeCheckByAdmin/{id}', 'changeCheckByAdmin');
});

Route::controller(EmployeeWorkController::class)->prefix('work')->group(function(){
    Route::post('/create', 'store');
});







