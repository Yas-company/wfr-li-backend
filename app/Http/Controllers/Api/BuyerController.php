<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    use ApiResponse;

    /**
     * Get buyer's orders with filtering capabilities
     */
    public function orders(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->filterByStatus($request->status)
            ->filterByPaymentStatus($request->payment_status)
            ->filterByPaymentMethod($request->payment_method)
            ->with(['items.product', 'receipt', 'supplier'])
            ->latest()
            ->paginate($request->input('per_page', 10));

        return $this->successResponse($orders);
    }

    /**
     * Get buyer's specific order details
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        // Check if the order belongs to the authenticated buyer
        if ($order->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('Unauthorized access to this order');
        }

        return $this->successResponse(
            $order->load(['items.product', 'receipt', 'supplier'])
        );
    }
} 