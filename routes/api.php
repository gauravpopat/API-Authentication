<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobApplicationController;
use App\Models\JobUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
    Route::get('job-list', 'list'); // List of jobs so user can apply for it.
});


//Logged In User
Route::middleware('auth:api')->group(function () {
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('show-user', 'show');
        Route::post('change-password', 'changePassword');
        Route::post('logout', 'logout');
        Route::post('apply-job', 'applyJob'); // Apply for the job
        Route::get('delete-user', 'delete');
    });
});

Route::middleware('isAdmin')->group(function () {
    //Company CRUD
    Route::controller(CompanyController::class)->prefix('company')->group(function () {
        Route::get('list', 'list');
        Route::post('create', 'create');
        Route::post('update', 'update');
        Route::get('show/{id}', 'show');
        Route::post('delete/{id}', 'delete');
    });

    //Job CRUD and List of JOB APPLICATION and APPROVE JOB APPLICATION
    Route::controller(JobController::class)->prefix('job')->group(function () {
        Route::get('list', 'list');
        Route::post('create', 'create');
        Route::post('update', 'update');
        Route::get('show/{id}', 'show');
        Route::post('delete/{id}', 'delete');
    });

    //Job Application: Approval of applications
    Route::controller(JobApplicationController::class)->prefix('application')->group(function () {
        //Accept the user application for job.
        Route::get('list', 'list');
        Route::post('approve', 'approve');
    });

    //Employee LIST,SHOW and DELETE (Employee Create -> When Application is approved then employee will be create)
    Route::controller(EmployeeController::class)->prefix('employee')->group(function () {
        Route::get('list', 'list');
        Route::get('show/{id}', 'show');
        Route::post('delete/{id}', 'delete');
    });
});
