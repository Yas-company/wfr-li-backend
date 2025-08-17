<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\api\v1\Payment\PaymentWebhookController;

Route::post('webhook', PaymentWebhookController::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('payment.webhook');
