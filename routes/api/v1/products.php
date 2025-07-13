<?php

use App\Http\Controllers\api\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::get('/expired/count', [ProductController::class, 'expiredCount']);
    Route::get('/near-expiry/count', [ProductController::class, 'nearExpiryCount']);
    Route::get('/stock-status/count', [ProductController::class, 'stockStatusCounts']);
});
