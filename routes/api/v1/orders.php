<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\BuyerOrderController;
use App\Http\Controllers\api\v1\SupplierOrderController;

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::prefix('supplier/orders')->group(function () {
        Route::get('/', [SupplierOrderController::class, 'index'])->name('supplier.orders.index');
        Route::get('/{order}', [SupplierOrderController::class, 'show'])->name('supplier.orders.show');
    });

    Route::prefix('buyer/orders')->group(function () {
        Route::get('/', [BuyerOrderController::class, 'index'])->name('buyer.orders.index');
        Route::get('/{order}', [BuyerOrderController::class, 'show'])->name('buyer.orders.show');
    });
});
