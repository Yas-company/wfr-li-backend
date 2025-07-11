<?php

namespace App\Http\Services\Payment;

use App\Http\Services\Contracts\PaymentStrategyInterface;

class PaymentContext
{
    protected PaymentStrategyInterface $strategy;

    public function setStrategy(PaymentStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function createPayment(array $data,$totals_discount): int
    {
        return $this->strategy->createPayment($data,$totals_discount);
    }

    public function verifyPayment(string $tap_id): array
    {
        return $this->strategy->verifyPayment($tap_id);
    }



}
