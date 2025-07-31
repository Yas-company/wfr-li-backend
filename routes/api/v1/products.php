<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\Product\Supplier\ProductController;
use App\Http\Controllers\api\v1\Product\Supplier\ProductMediaController;
use App\Http\Controllers\api\v1\Product\Buyer\ProductController as BuyerProductController;
use App\Http\Middleware\RoleMiddleware;

Route::middleware(['auth:sanctum', RoleMiddleware::class .':'.UserRole::SUPPLIER->value])->prefix('supplier/products')->group(function () {
    Route::get('/available', [ProductController::class, 'getAvailableProducts'])->name('products.available');
    Route::get('/out-of-stock', [ProductController::class, 'getOutOfStockProducts'])->name('products.out-of-stock');
    Route::get('/nearly-out-of-stock', [ProductController::class, 'getNearlyOutOfStockProducts'])->name('products.nearly-out-of-stock');

    Route::get('/', [ProductController::class, 'index'])->name('supplier.products.index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('supplier.products.show');
    Route::post('/', [ProductController::class, 'store'])->name('supplier.products.store');
    Route::post('/{product}', [ProductController::class, 'update'])->name('supplier.products.update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('supplier.products.destroy');

    Route::get('/expired/count', [ProductController::class, 'expiredCount'])->name('supplier.products.expired.count');
    Route::get('/near-expiry/count', [ProductController::class, 'nearExpiryCount'])->name('supplier.products.near-expiry.count');

    Route::post('/{product}/attach-media', [ProductMediaController::class, 'store'])->name('supplier.products.attach-media');
    Route::delete('/{product}/media/{media}', [ProductMediaController::class, 'destroy'])->name('supplier.products.media.destroy');

});

Route::middleware(['auth:sanctum', RoleMiddleware::class .':'.UserRole::BUYER->value])->prefix('buyer/products')->group(function () {
    Route::get('/', [BuyerProductController::class, 'index'])->name('buyer.products.index');
    Route::get('/{product}', [BuyerProductController::class, 'show'])->name('buyer.products.show');
});

