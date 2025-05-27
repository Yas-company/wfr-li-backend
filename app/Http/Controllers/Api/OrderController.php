<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
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

    public function accept(Order $order): JsonResponse
    {
        if ($order->status !== OrderStatus::PENDING->value) {
            return response()->json(['message' => 'Order cannot be accepted'], 400);
        }

        $order = $this->orderService->updateOrderStatus($order, OrderStatus::ACCEPTED);

        return response()->json([
            'message' => 'Order accepted successfully',
            'order' => $order->load('items.product', 'receipt'),
        ]);
    }

    public function reject(Order $order): JsonResponse
    {
        if ($order->status !== OrderStatus::PENDING->value) {
            return response()->json(['message' => 'Order cannot be rejected'], 400);
        }

        $order = $this->orderService->updateOrderStatus($order, OrderStatus::REJECTED);

        return response()->json([
            'message' => 'Order rejected successfully',
            'order' => $order->load('items.product'),
        ]);
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

    public function updateShippingStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:shipped,delivered',
            'tracking_number' => 'nullable|string',
        ]);

        if ($order->status !== OrderStatus::PAID->value) {
            return response()->json(['message' => 'Order is not paid'], 400);
        }

        $order = $this->orderService->updateShippingStatus(
            $order,
            OrderStatus::from($request->status),
            $request->tracking_number
        );

        return response()->json([
            'message' => 'Shipping status updated successfully',
            'order' => $order->load('items.product'),
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'order' => $order->load('items.product', 'receipt', 'supplier'),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Order::query();

        if ($request->user()->isBuyer()) {
            $query->where('user_id', $request->user()->id);
        }

        // Filter by order status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->with('items.product', 'receipt', 'supplier')
            ->latest()
            ->paginate(10);

        return response()->json(['orders' => $orders]);
    }
} 