<?php

namespace App\Http\Services\Implementations;

use App\Http\Services\Contracts\OrderServiceInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService implements OrderServiceInterface
{
    public function checkout(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $data['total'],
                'payment_method_id' => $data['payment_method_id'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            return $order->load(['items', 'paymentMethod']);
        });
    }
}
