<?php

use App\Http\Controllers\AdsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OnboardingScreenController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/onboarding-screens', [OnboardingScreenController::class, 'index']);
Route::get('/ads', [AdsController::class, 'index']);
Route::get('/fields', [FieldController::class, 'index']);
Route::get('/fields/{field}', [FieldController::class, 'show']);

// Buyer Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('biometric-login', [AuthController::class, 'biometricLogin']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::delete('delete-account', [AuthController::class, 'destroy']);

        //suppliers
        Route::get('/suppliers', [UserController::class, 'suppliers']);
        Route::get('/suppliers/{user}', [UserController::class, 'show']);

        //categories
        Route::post('/categories', [CategoryController::class, 'store']);




        //temp routes
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/filter', [ProductController::class, 'filter']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::get('/products/{product}/related', [ProductController::class, 'related']);


        // Cart Routes
        Route::get('/cart', [CartController::class, 'getCart']);
        Route::post('/cart/add', [CartController::class, 'addToCart']);
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeFromCart']);
        Route::patch('/cart/items/{cartItem}/quantity', [CartController::class, 'updateQuantity']);

        // Favorites Routes
        Route::get('/favorites', [\App\Http\Controllers\FavoritesController::class, 'index']);
        Route::post('/favorites', [\App\Http\Controllers\FavoritesController::class, 'store']);
        Route::delete('/favorites/{product}', [\App\Http\Controllers\FavoritesController::class, 'destroy']);

        // Order Routes
        Route::post('/orders/checkout', [App\Http\Controllers\Api\OrderController::class, 'checkout']);
        Route::get('/orders', [App\Http\Controllers\Api\BuyerController::class, 'orders']);
        Route::get('/orders/{order}', [App\Http\Controllers\Api\BuyerController::class, 'show']);

        // Payment routes
        Route::post('/orders/{order}/payment-status', [App\Http\Controllers\Api\OrderController::class, 'updatePaymentStatus']);
    });
});