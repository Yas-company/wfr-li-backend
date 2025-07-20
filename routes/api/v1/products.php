<?php

use App\Http\Controllers\api\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/expired/count', [ProductController::class, 'expiredCount'])->name('products.expired.count');
    Route::get('/near-expiry/count', [ProductController::class, 'nearExpiryCount'])->name('products.near-expiry.count');
    Route::get('/stock-status/count', [ProductController::class, 'stockStatusCounts'])->name('products.stock-status.count');
});

Route::middleware('auth:sanctum')->prefix('products')->group(function () {

    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::post('/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});
