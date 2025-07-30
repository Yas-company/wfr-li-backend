<?php

use App\Http\Controllers\api\v1\Supplier\SupplierProfileController;
use App\Http\Controllers\api\v1\Supplier\SupplierSettingController;
use App\Http\Controllers\api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    // suppliers
    Route::get('/suppliers', [UserController::class, 'suppliers']);
    Route::post('/suppliers/search', [UserController::class, 'searchSuppliers']);
    Route::get('/suppliers/filter', [UserController::class, 'filter']);
    Route::put('/suppliers/setting', [SupplierSettingController::class, 'update']);
    Route::put('/suppliers/profile', [SupplierProfileController::class, 'updateSupplierProfile'])->name('suppliers.profile.update');
    Route::post('/suppliers/update-image', [SupplierProfileController::class, 'changeSupplierImage'])->name('suppliers.image.change');

    // This should be LAST to avoid catching specific routes
    Route::get('/suppliers/{user}', [UserController::class, 'show']);
});
