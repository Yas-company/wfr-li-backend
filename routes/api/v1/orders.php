<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\api\v1\BuyerOrderController;
use App\Http\Controllers\api\v1\SupplierOrderController;

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::prefix('supplier/orders')->middleware(RoleMiddleware::class. ':'.UserRole::SUPPLIER->value)->group(function () {
        Route::get('/', [SupplierOrderController::class, 'index'])->name('supplier.orders.index');
        Route::get('/{order}', [SupplierOrderController::class, 'show'])->name('supplier.orders.show');
    });

    Route::prefix('buyer/orders')->middleware(RoleMiddleware::class.':'.UserRole::BUYER->value)->group(function () {
        Route::get('/', [BuyerOrderController::class, 'index'])->name('buyer.orders.index');
        Route::get('/{order}', [BuyerOrderController::class, 'show'])->name('buyer.orders.show');
    });
});
