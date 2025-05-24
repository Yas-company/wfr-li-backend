<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\BuyerAuthController;
use App\Http\Controllers\Auth\SupplierAuthController;
use App\Http\Controllers\OnboardingScreenController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/onboarding-screens', [OnboardingScreenController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/filter', [ProductController::class, 'filter']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/related', [ProductController::class, 'related']);

// Buyer Authentication Routes
Route::prefix('buyer')->group(function () {
    Route::post('/register', [BuyerAuthController::class, 'register']);
    Route::post('/login', [BuyerAuthController::class, 'login']);
    
    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [BuyerAuthController::class, 'logout']);
        Route::get('/me', [BuyerAuthController::class, 'me']);
        Route::post('/change-password', [BuyerAuthController::class, 'changePassword']);

        // Cart Routes
        Route::get('/cart', [CartController::class, 'getCart']);
        Route::post('/cart/add', [CartController::class, 'addToCart']);
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeFromCart']);
        Route::patch('/cart/items/{cartItem}/quantity', [CartController::class, 'updateQuantity']);

        // Favorites Routes
        Route::get('/favorites', [\App\Http\Controllers\FavoritesController::class, 'index']);
        Route::post('/favorites', [\App\Http\Controllers\FavoritesController::class, 'store']);
        Route::delete('/favorites/{product}', [\App\Http\Controllers\FavoritesController::class, 'destroy']);
    });

    // Password Reset Routes
    Route::post('forgot-password', [BuyerAuthController::class, 'forgotPassword']);
    Route::post('verify-otp', [BuyerAuthController::class, 'verifyOtp']);
    Route::post('reset-password', [BuyerAuthController::class, 'resetPassword']);
});

// Supplier Authentication Routes
Route::prefix('supplier')->group(function () {
    Route::post('/login', [SupplierAuthController::class, 'login']);
    
    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [SupplierAuthController::class, 'logout']);
        Route::get('/me', [SupplierAuthController::class, 'me']);
    });
});