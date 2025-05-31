<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'shipping_address' => 'nullable|string',
            'shipping_latitude' => 'nullable|numeric',
            'shipping_longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,visa',
        ]);

        $cart = $request->user()->cart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Use user's address if not provided
        $data = $request->all();
        $data['shipping_address'] = $data['shipping_address'] ?? $request->user()->address;
        $data['shipping_latitude'] = $data['shipping_latitude'] ?? $request->user()->latitude;
        $data['shipping_longitude'] = $data['shipping_longitude'] ?? $request->user()->longitude;

        $order = $this->orderService->createOrderFromCart($cart, $data);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('items.product'),
        ], 201);
    }

    public function updatePaymentStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed',
            'payment_id' => 'nullable|string',
        ]);

        if ($order->status !== OrderStatus::ACCEPTED->value) {
            return response()->json(['message' => 'Order is not in accepted state'], 400);
        }

        $order = $this->orderService->updatePaymentStatus(
            $order,
            $request->status,
            $request->payment_id
        );

        return response()->json([
            'message' => 'Payment status updated successfully',
            'order' => $order->load('items.product', 'receipt'),
        ]);
    }
} 