<?php

use App\Http\Controllers\api\v1\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::put('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});
