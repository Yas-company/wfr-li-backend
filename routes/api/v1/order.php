<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('checkout', [OrderController::class, 'checkout']);
    Route::get('payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('my-orders', [OrderController::class, 'myOrders']);
    Route::get('my-orders/{id}', [OrderController::class, 'show']);
    Route::post('upload-payment-proof', [OrderController::class, 'uploadPaymentProof']);
});
