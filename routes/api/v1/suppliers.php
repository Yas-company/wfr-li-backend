<?php

use App\Http\Controllers\api\v1\SupplierController;
use App\Http\Controllers\api\v1\SupplierProfileController;
use App\Http\Controllers\api\v1\SupplierSettingController;
use App\Http\Controllers\api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    // suppliers
    Route::get('/suppliers', [UserController::class, 'suppliers']);
    Route::post('/suppliers/search', [UserController::class, 'searchSuppliers']);
    Route::get('/suppliers/filter', [UserController::class, 'filter']);
    Route::get('/suppliers/{user}', [UserController::class, 'show']);
    Route::put('/suppliers/setting', [SupplierSettingController::class, 'update']);

    Route::put('/suppliers/profile', [SupplierProfileController::class, 'updateSupplierProfile'])->name('suppliers.profile.update');

    Route::get('/suppliers/products/available', [SupplierController::class, 'getAvailableProducts']);
    Route::get('/suppliers/products/nearly-out-of-stock', [SupplierController::class, 'getNearlyOutOfStockProducts']);
    Route::get('/suppliers/products/out-of-stock', [SupplierController::class, 'getOutOfStockProducts']);
});
