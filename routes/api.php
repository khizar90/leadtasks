<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserDetailController;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('user/verify', [AuthController::class, 'verify']);
Route::post('otp/verify', [AuthController::class, 'otpVerify']);
Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login']);
Route::post('user/social/login', [AuthController::class, 'socialLogin']);
Route::post('user/recover', [AuthController::class, 'recover']);
Route::post('user/new/password', [AuthController::class, 'newPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('change/password', [AuthController::class, 'changePassword']);
    Route::post('user/logout', [AuthController::class, 'logout']);
    Route::post('user/delete', [AuthController::class, 'deleteAccount']);
    Route::post('edit/image', [AuthController::class, 'editImage']);
    Route::get('remove/image/', [AuthController::class, 'removeImage']);
    Route::post('edit/profile', [AuthController::class, 'editProfile']);


    Route::post('user/add/education', [UserDetailController::class, 'addEducation']);
    Route::get('user/list/education', [UserDetailController::class, 'listEducation']);
    Route::get('user/delete/education/{id}', [UserDetailController::class, 'deleteEducation']);
    Route::post('user/add/skill', [UserDetailController::class, 'addSkill']);
    Route::get('user/list/skill', [UserDetailController::class, 'listSkill']);
    Route::get('user/delete/skill/{id}', [UserDetailController::class, 'deleteSkill']);
    Route::post('user/add/portfolio', [UserDetailController::class, 'addPortfolio']);
    Route::get('user/delete/portfolio/{id}', [UserDetailController::class, 'deletePortfolio']);
    
    

    Route::post('create/job', [JobController::class, 'create']);
    Route::get('job/home', [JobController::class, 'index']);
    Route::get('job/detail/{job_id}', [JobController::class, 'detail']);
    Route::get('job/save/{job_id}', [JobController::class, 'save']);
    Route::get('job/list/{type}', [JobController::class, 'list']);


    Route::get('user/profile/{id}', [UserController::class, 'profile']);
    Route::post('user/apply/job', [UserController::class, 'applyJob']);
    Route::get('user/applied/job/{id}', [UserController::class, 'ListJobs']);
    Route::get('user/job/applied/detail/{id}', [UserController::class, 'appliedJobDetail']);
    Route::get('user/job/change/status/{status}/{ofer_id}', [UserController::class, 'changeStatus']);
    Route::get('user/my/jobs/{type}', [UserController::class, 'myJobs']);
    Route::get('user/see/jobs/offer/{job_id}', [UserController::class, 'seeOffer']);

    Route::post('send/message', [MessageController::class, 'sendMessage']);


});
Route::get('splash/{user_id?}' , [SettingController::class , 'splash']);
Route::get('faqs', [SettingController::class, 'faqs']);

