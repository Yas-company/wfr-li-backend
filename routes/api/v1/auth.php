<?php

use App\Http\Controllers\api\v1\Auth\BuyerLoginController;
use App\Http\Controllers\api\v1\Auth\SupplierLoginController;
use App\Http\Controllers\api\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('supplier/login', SupplierLoginController::class);
    Route::post('buyer/login', BuyerLoginController::class);
    Route::post('biometric-login', [AuthController::class, 'biometricLogin']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::delete('delete-account', [AuthController::class, 'destroy']);
    });
});
