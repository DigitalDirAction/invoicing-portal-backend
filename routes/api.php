<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register_user', [UserController::class, 'store']);
Route::post('/login_user', [UserController::class, 'login']);
Route::post('/verify_two_factor_token', [UserController::class, 'verifyTwoAuth']);
Route::post('/resend_two_factor_token/{email}', [UserController::class, 'resendTwoFactorCode']);
Route::post('/email/resend-verification/{email}', [UserController::class, 'resendVerificationEmail']);
Route::post('/forgot_password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset_password/{token}', [PasswordResetController::class, 'resetPassword']);
// Route for logging out the user
Route::post('/user_profile', [UserController::class, 'userProfile']);
Route::middleware('auth:sanctum')->post('/logout_user', [UserController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/login_user_data/{user_id}', [UserController::class, 'getLoginUserData']);
Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    // Route::post('/user_profile', [UserController::class, 'userProfile']);
});