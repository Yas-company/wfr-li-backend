<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private OrderService $orderService
    ) {}

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shipping_address' => 'nullable|string',
            'shipping_latitude' => 'nullable|numeric',
            'shipping_longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,visa',
        ]);

        $cart = $request->user()->cart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return $this->errorResponse('Cart is empty', null, Response::HTTP_BAD_REQUEST);
        }

        // Use user's address if not provided
        $data = $request->all();
        $data['shipping_address'] = $data['shipping_address'] ?? $request->user()->address;
        $data['shipping_latitude'] = $data['shipping_latitude'] ?? $request->user()->latitude;
        $data['shipping_longitude'] = $data['shipping_longitude'] ?? $request->user()->longitude;

        $order = $this->orderService->createOrderFromCart($cart, $data);

        return $this->createdResponse(
            $order->load('items.product'),
            'Order created successfully'
        );
    }

    public function updatePaymentStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed',
            'payment_id' => 'nullable|string',
        ]);

        if ($order->status !== OrderStatus::ACCEPTED->value) {
            return $this->errorResponse('Order is not in accepted state', null, Response::HTTP_BAD_REQUEST);
        }

        $order = $this->orderService->updatePaymentStatus(
            $order,
            $request->status,
            $request->payment_id
        );

        return $this->successResponse(
            $order->load('items.product', 'receipt'),
            'Payment status updated successfully'
        );
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with(['items.product', 'supplier'])
            ->latest()
            ->paginate(10);

        return $this->successResponse($orders);
    }
} 