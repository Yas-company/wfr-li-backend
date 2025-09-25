<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\api\v1\Payment\ProcessPaymentCallbackController;

Route::match(['GET', 'POST'], 'callback', ProcessPaymentCallbackController::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('payment.callback');
