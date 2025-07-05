<?php

namespace App\Http\Services\Payment;

use App\Http\Services\Contracts\PaymentStrategyInterface;
use App\Http\Services\Payment\Strategies\CashOnDeliveryStrategy;
use App\Http\Services\Payment\Strategies\TapPaymentStrategy;
// use App\Services\Payment\Strategies\PaypalPaymentStrategy; // لو ضفت لاحقًا

class PaymentFactory
{
    /**
     *
     * @param string $method
     * @return PaymentStrategyInterface
     */
    public static function make(string $method): PaymentStrategyInterface
    {
        return match ($method) {
            'tap' => new TapPaymentStrategy(),
            'cash_on_delivery' => new CashOnDeliveryStrategy(),
            // 'paypal' => new PaypalPaymentStrategy(), ← مثال لو أضفت لاحقًا
            default => throw new \InvalidArgumentException("Unsupported payment method: $method")
        };
    }
}
