<?php

use App\Http\Controllers\api\v1\Product\ProductController;
use App\Http\Controllers\api\v1\Product\ProductMediaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::get('/available', [ProductController::class, 'getAvailableProducts'])->name('products.available');
    Route::get('/out-of-stock', [ProductController::class, 'getOutOfStockProducts'])->name('products.out-of-stock');
    Route::get('/nearly-out-of-stock', [ProductController::class, 'getNearlyOutOfStockProducts'])->name('products.nearly-out-of-stock');

    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/expired/count', [ProductController::class, 'expiredCount'])->name('products.expired.count');
    Route::get('/near-expiry/count', [ProductController::class, 'nearExpiryCount'])->name('products.near-expiry.count');
    Route::get('/stock-status/count', [ProductController::class, 'stockStatusCounts'])->name('products.stock-status.count');
    Route::post('/{product}/attach-media', [ProductMediaController::class, 'store'])->name('products.attach-media');
    Route::delete('/{product}/media/{media}', [ProductMediaController::class, 'destroy'])->name('products.media.destroy');

});

Route::middleware('auth:sanctum')->prefix('products')->group(function () {

    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::post('/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});

