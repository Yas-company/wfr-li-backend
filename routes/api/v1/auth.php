<?php

use App\Http\Controllers\api\v1\Auth\Buyer\BuyerLoginController;
use App\Http\Controllers\api\v1\Auth\Buyer\BuyerRegistrationController;
use App\Http\Controllers\api\v1\Auth\OtpController;
use App\Http\Controllers\api\v1\Auth\Supplier\SupplierLoginController;
use App\Http\Controllers\api\v1\Auth\Supplier\SupplierRegistrationController;
use App\Http\Controllers\api\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::prefix('buyer')->group(function () {
        Route::post('register', BuyerRegistrationController::class)->name('auth.buyer.register');
        Route::post('login', BuyerLoginController::class)->name('auth.buyer.login');
    });

    Route::prefix('supplier')->group(function () {
        Route::post('register', SupplierRegistrationController::class)->name('auth.supplier.register');
        Route::post('login', SupplierLoginController::class)->name('auth.supplier.login');
    });

    Route::post('verify-otp', [OtpController::class, 'verifyOtp'])->name('auth.verify-otp');
    Route::post('request-otp', [OtpController::class, 'requestOtp'])->middleware('throttle:2,1')->name('auth.request-otp');
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
