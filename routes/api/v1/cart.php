<?php

use App\Http\Controllers\api\v1\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store']);
    Route::delete('/{product}', [CartController::class, 'destroy']);
    Route::put('/clear', [CartController::class, 'clear']);
});
