<?php

use App\Http\Controllers\api\v1\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('payment')->group(function () {
    Route::post('create', [PaymentController::class, 'create']);
    Route::get('callback', [PaymentController::class, 'callback'])->name('payment.callback');
});
