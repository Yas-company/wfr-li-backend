<?php

namespace App\Http\Services\Payment;

use App\Enums\Order\PaymentStatus;
use App\Http\Services\Contracts\PaymentServiceInterface;
use App\Models\Ads;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService implements PaymentServiceInterface
{
    public function storePayment($request,array $data)
    {
        $status = match ($data['status']) {
            'CAPTURED' => PaymentStatus::PAID,
            'FAILED' => PaymentStatus::CANCELLED,
            default => PaymentStatus::PENDING,
        };

        Payment::updateOrCreate(
            ['tap_id' => $request->tap_id],
            [
                'reference_id' => $data['reference']['transaction'] ?? null,
                'payment_method' => $data['source']['payment_type'] ?? null,
                'status' => $status,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'user_id' => auth()->id(),
            ]
        );
    }

}
