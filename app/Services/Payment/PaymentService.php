<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\Contracts\PaymentGatewayInterface;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway
    ) {}

    public function redirect(Order $order): string
    {
        return $this->gateway->redirect($order);
    }

    public function handleCallback(Request $request): array
    {
        return $this->gateway->handleCallback($request);
    }
}
