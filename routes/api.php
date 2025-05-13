<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\BuyerAuthController;
use App\Http\Controllers\OnboardingScreenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/onboarding-screens', [OnboardingScreenController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Buyer Authentication Routes
Route::prefix('buyer')->group(function () {
    Route::post('/register', [BuyerAuthController::class, 'register']);
    Route::post('/login', [BuyerAuthController::class, 'login']);
    
    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [BuyerAuthController::class, 'logout']);
        Route::get('/me', [BuyerAuthController::class, 'me']);
        Route::post('/change-password', [BuyerAuthController::class, 'changePassword']);
    });

    // Password Reset Routes
    Route::post('forgot-password', [BuyerAuthController::class, 'forgotPassword']);
    Route::post('verify-otp', [BuyerAuthController::class, 'verifyOtp']);
    Route::post('reset-password', [BuyerAuthController::class, 'resetPassword']);
});
