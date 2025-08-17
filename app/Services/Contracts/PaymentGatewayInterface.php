<?php

namespace App\Services\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Redirect user to payment page or return payment URL.
     */
    public function initiatePayment(Order $order): array;

    /**
     * Handle callback/webhook and return payment result.
     */
    public function webhook(Request $request): array;

    /**
     * Redirect user to payment page or return payment URL.
     */
    public function redirect(Request $request): string;
}
