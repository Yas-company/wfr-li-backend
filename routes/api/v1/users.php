<?php

use App\Http\Controllers\api\v1\AddressController;
use Illuminate\Support\Facades\Route;

Route::prefix('addresses')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AddressController::class, 'index']);
    Route::post('/', [AddressController::class, 'store']);
    Route::put('/{address}', [AddressController::class, 'update']);
    Route::delete('/{address}', [AddressController::class, 'destroy']);
});
