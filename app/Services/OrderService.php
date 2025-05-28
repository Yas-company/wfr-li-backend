<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Receipt;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function createOrderFromCart(Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($cart, $data) {
            // Create the order
            $order = Order::create([
                'user_id' => $cart->user_id,
                'supplier_id' => $data['supplier_id'],
                'status' => OrderStatus::PENDING->value,
                'total_amount' => $cart->total_amount,
                'shipping_address' => $data['shipping_address'],
                'shipping_latitude' => $data['shipping_latitude'] ?? null,
                'shipping_longitude' => $data['shipping_longitude'] ?? null,
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'] ?? PaymentMethod::CASH,
                'payment_status' => 'pending',
            ]);

            // Create order items from cart items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ]);
            }

            // Clear the cart
            $cart->items()->delete();
            $cart->delete();

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, OrderStatus $status): Order
    {
        $order->update(['status' => $status->value]);

        if ($status === OrderStatus::ACCEPTED) {
            $this->createReceipt($order);
        }

        return $order;
    }

    public function createReceipt(Order $order): Receipt
    {
        return Receipt::create([
            'order_id' => $order->id,
            'receipt_number' => 'REC-' . strtoupper(Str::random(8)),
            'total_amount' => $order->total_amount,
            'payment_status' => 'pending',
            'payment_method' => $order->payment_method,
        ]);
    }

    public function updatePaymentStatus(Order $order, string $status, ?string $paymentId = null): Order
    {
        $order->update([
            'payment_status' => $status,
            'payment_id' => $paymentId,
        ]);

        if ($status === 'paid') {
            $order->update(['status' => OrderStatus::PAID->value]);
            $order->receipt->update([
                'payment_status' => 'paid',
                'payment_date' => now(),
            ]);
        }

        return $order;
    }

    public function updateShippingStatus(Order $order, OrderStatus $status, ?string $trackingNumber = null): Order
    {
        $order->update([
            'status' => $status->value,
            'tracking_number' => $trackingNumber,
        ]);

        return $order;
    }
} 