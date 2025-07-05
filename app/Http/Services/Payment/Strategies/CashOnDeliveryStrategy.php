<?php

namespace App\Http\Services\Payment\Strategies;

use App\Enums\PaymentMethod;
use App\Http\Services\Contracts\PaymentStrategyInterface;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class CashOnDeliveryStrategy implements PaymentStrategyInterface
{
    public function createPayment(array $data): array
    {
        $payment = Payment::create([
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY,
            'status' => PaymentStatus::PENDING,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'SAR',
            'user_id' => auth()->id(),
        ]);

        return [
            'message' => 'تم تسجيل الدفع عند التوصيل',
            'payment_id' => $payment->id,
            'status' => $payment->status->value
        ];
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
