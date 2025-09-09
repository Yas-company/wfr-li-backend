<?php

use App\Http\Controllers\api\v1\AddressController;
use App\Http\Controllers\api\v1\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Enums\UserRole;

Route::prefix('addresses')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
});
