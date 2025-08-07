<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\Order\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Api\V1\Order\Supplier\ChangeOrderStatusController;
use App\Http\Controllers\api\v1\Order\Supplier\OrderController as SupplierOrderController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::prefix('supplier/orders')->middleware(RoleMiddleware::class.':'.UserRole::SUPPLIER->value)->group(function () {
        Route::get('/', [SupplierOrderController::class, 'index'])->name('supplier.orders.index');
        Route::get('/{order}', [SupplierOrderController::class, 'show'])->name('supplier.orders.show');
        Route::post('/{order}/change-status', ChangeOrderStatusController::class)->name('supplier.orders.change-status');
    });

    Route::prefix('buyer/orders')->middleware(RoleMiddleware::class.':'.UserRole::BUYER->value)->group(function () {
        Route::get('/', [BuyerOrderController::class, 'index'])->name('buyer.orders.index');
        Route::get('/{order}', [BuyerOrderController::class, 'show'])->name('buyer.orders.show');
        Route::post('/{order}/reorder', [BuyerOrderController::class, 'reorder'])->name('buyer.orders.reorder');
    });
});
