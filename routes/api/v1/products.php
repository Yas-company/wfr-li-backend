<?php

use App\Http\Controllers\api\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::get('/expired/count', [ProductController::class, 'expiredCount']);
    Route::get('/near-expiry/count', [ProductController::class, 'nearExpiryCount']);
    Route::get('/stock-status/count', [ProductController::class, 'stockStatusCounts']);
});

Route::middleware('auth:sanctum')->prefix('products')->group(function() {

    Route::post('/', [ProductController::class, 'store']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});
