<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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


//Guest User
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::get('verify-email/{verificationCode}','verifyEmail');
    Route::post('forgot-password-link','forgotPasswordLink');
    Route::post('forgot-password','forgotPassword');
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
});
