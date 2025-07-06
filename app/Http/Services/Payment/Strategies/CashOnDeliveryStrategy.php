<?php

namespace App\Http\Services\Payment\Strategies;

use App\Enums\Order\PaymentMethod;
use App\Enums\Order\PaymentStatus;
use App\Http\Services\Contracts\PaymentStrategyInterface;
use App\Models\Payment;

class CashOnDeliveryStrategy implements PaymentStrategyInterface
{

    public function createPayment(array $data,$totals_discount): int
    {
        $payment = Payment::create([
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY,
            'status' => PaymentStatus::PENDING,
            'amount' => $totals_discount,
            'currency' => $data['currency'] ?? 'SAR',
            'user_id' => auth()->id(),
        ]);

        return $payment->id;
    }


    public function verifyPayment(string $id): array
    {
        $payment = Payment::findOrFail($id);

        return [
            'status' => $payment->status->value,
            'method' => $payment->payment_method,
            'amount' => $payment->amount,
            'currency' => $payment->currency
        ];
    }
}
