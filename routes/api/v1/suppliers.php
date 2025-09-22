<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\Profile\Supplier\ProfileController;
use App\Http\Controllers\api\v1\Supplier\GetBuyersController;
use App\Http\Controllers\api\v1\SupplierSettingController;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', RoleMiddleware::class.':'.UserRole::SUPPLIER->value])->group(function () {
    Route::get('suppliers/buyers', GetBuyersController::class)->name('suppliers.buyers.index');
    Route::put('/suppliers/profile', [ProfileController::class, 'updateSupplierProfile'])->name('suppliers.profile.update');
    Route::post('/suppliers/image', [ProfileController::class, 'changeSupplierImage'])->name('suppliers.image.change');
    Route::delete('/suppliers/profile', [ProfileController::class, 'destroy'])->name('suppliers.profile.delete');
    Route::put('/suppliers/change-phone', [ProfileController::class, 'updateSupplierPhone'])->name('suppliers.phone.update');
    Route::put('/suppliers/set-setting', [SupplierSettingController::class, 'setSetting'])->name('suppliers.setting.set');
    Route::get('/suppliers/settings', [SupplierSettingController::class, 'getSupplierSettings'])->name('suppliers.settings.get');
    Route::put('/suppliers/setting', [SupplierSettingController::class, 'update'])->name('suppliers.setting.update');

});

Route::group(['middleware' => 'auth:sanctum'], function () {

    // suppliers
    Route::get('/suppliers', [UserController::class, 'suppliers']);
    Route::post('/suppliers/search', [UserController::class, 'searchSuppliers']);
    Route::get('/suppliers/filter', [UserController::class, 'filter']);
    Route::get('/suppliers/{user}', [UserController::class, 'show']);
});
