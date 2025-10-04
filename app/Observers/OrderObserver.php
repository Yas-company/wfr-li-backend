<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order): void
    {
        $order->statusHistories()->create([
            'changed_by' => auth()->id(),
            'old_status' => null,
            'new_status' => $order->status,
        ]);
    }

    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            $order->statusHistories()->create([
                'changed_by' => auth()->id(),
                'old_status' => $order->getOriginal('status'),
                'new_status' => $order->status,
            ]);
        }
    }
}
