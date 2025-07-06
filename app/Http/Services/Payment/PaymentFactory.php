<?php

namespace App\Http\Services\Payment;

use App\Enums\Order\PaymentMethod;
use App\Http\Services\Contracts\PaymentStrategyInterface;
use App\Http\Services\Payment\Strategies\CashOnDeliveryStrategy;
use App\Http\Services\Payment\Strategies\TapPaymentStrategy;
// use App\Services\Payment\Strategies\PaypalPaymentStrategy;

class PaymentFactory
{
    /**
     *
     * @param string $method
     * @return PaymentStrategyInterface
     */
    public static function make(string $method): PaymentStrategyInterface
    {
        return match (PaymentMethod::from((int) $method)) {
            PaymentMethod::Tap => new TapPaymentStrategy(),
            PaymentMethod::CASH_ON_DELIVERY => new CashOnDeliveryStrategy(),
            // 'paypal' => new PaypalPaymentStrategy(),
            default => throw new \InvalidArgumentException("Unsupported payment method: $method")
        };
    }
}
