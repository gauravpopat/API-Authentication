<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserJobController;
use App\Models\JobUser;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//Custom Message When User is Unauthenticated -> app\http\middleware\authenticate.php
Route::get('unauthenticated', function () {
    return error('unauthenticated', '', 'unauthenticated');
})->name('unauthenticated');

//Guest User
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::get('verify-email/{verificationCode}', 'verifyEmail');
    Route::post('forgot-password-link', 'forgotPasswordLink');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('login', 'login');
});

//Logged In User
Route::middleware('auth:api')->group(function () {
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('show-user', 'show');
        Route::post('change-password', 'changePassword');
        Route::post('logout', 'logout');
        Route::get('delete-user', 'delete');
    });

    //JobUsers : Apply for job
    Route::controller(UserJobController::class)->prefix('apply')->group(function () {
        Route::get('job-list', 'list');
        Route::post('job-apply', 'create');
    });
});

//Company
Route::controller(CompanyController::class)->prefix('company')->group(function () {
    Route::get('list', 'list');
    Route::post('create', 'create');
    Route::post('update', 'update');
    Route::get('show/{id}', 'show');
    Route::post('delete/{id}', 'delete');
});

//Jobs
Route::controller(JobController::class)->prefix('job')->group(function () {
    Route::get('list', 'list');
    Route::post('create', 'create');
    Route::post('update', 'update');
    Route::get('show/{id}', 'show');
    Route::post('delete/{id}', 'delete');

    //Accept the user application for job.
    Route::get('list-application', 'listApplication');
    Route::post('approve-application', 'approveApplication');
});
