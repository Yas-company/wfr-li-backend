<?php

use App\Http\Controllers\api\v1\Auth\BuyerLoginController;
use App\Http\Controllers\api\v1\Auth\SupplierLoginController;
use App\Http\Controllers\api\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp');
    Route::post('supplier/login', SupplierLoginController::class)->name('auth.supplier.login');
    Route::post('buyer/login', BuyerLoginController::class)->name('auth.buyer.login');
    Route::post('biometric-login', [AuthController::class, 'biometricLogin'])->name('auth.biometric-login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
        Route::delete('delete-account', [AuthController::class, 'destroy'])->name('auth.delete-account');
    });
});
