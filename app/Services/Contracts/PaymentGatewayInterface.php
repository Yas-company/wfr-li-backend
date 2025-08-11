<?php

namespace App\Services\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Redirect user to payment page or return payment URL.
     */
    public function redirect(Order $order): string;

    /**
     * Handle callback/webhook and return payment result.
     */
    public function handleCallback(Request $request): array;
}
