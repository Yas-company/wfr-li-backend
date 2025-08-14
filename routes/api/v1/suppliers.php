<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\SupplierProfileController;
use App\Http\Controllers\api\v1\SupplierSettingController;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    // suppliers
    Route::get('/suppliers', [UserController::class, 'suppliers']);
    Route::post('/suppliers/search', [UserController::class, 'searchSuppliers']);
    Route::get('/suppliers/filter', [UserController::class, 'filter']);
    Route::get('/suppliers/{user}', [UserController::class, 'show']);
    Route::put('/suppliers/setting', [SupplierSettingController::class, 'update']);



});

Route::middleware(['auth:sanctum', RoleMiddleware::class.':'.UserRole::SUPPLIER->value])->group(function () {
    Route::put('/suppliers/profile', [SupplierProfileController::class, 'updateSupplierProfile'])->name('suppliers.profile.update');
    Route::post('/suppliers/image', [SupplierProfileController::class, 'changeSupplierImage'])->name('suppliers.image.change');
});
