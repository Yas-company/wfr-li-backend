<?php

namespace App\Jobs;

use Algolia\AlgoliaSearch\Model\Search\Log;
use App\Models\Order;
use App\Enums\Order\OrderStatus;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpirePendingOrdersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredOrders = Order::query()
                ->with('products', 'products.product')
                ->whereIn('status', [OrderStatus::PENDING_PAYMENT, OrderStatus::FAILED])
                ->where('expires_at', '<', now())
                ->where('is_expired', false)
                ->lockForUpdate()
                ->get();

        foreach ($expiredOrders as $order)
        {
            foreach ($order->products as $item)
            {
                $item->product->lockForUpdate()->increment('stock_qty', $item->quantity);
            }

            $order->reservedStock()->delete();

            if ($order->status === OrderStatus::PENDING_PAYMENT) {
                $order->update(['status' => OrderStatus::EXPIRED]);
            }

            $order->update(['is_expired' => true]);
        }
    }
}
