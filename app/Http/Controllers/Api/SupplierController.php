<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SupplierLocationService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierLocationService $locationService,
        private readonly OrderService $orderService
    ) {}

    /**
     * Get the nearest supplier and their products
     */
    public function nearest(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        $user = $request->user();
        
        // Update user's location
        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        $nearestSupplier = $this->locationService->findNearestSupplier($user);

        if (!$nearestSupplier) {
            return response()->json([
                'message' => 'No nearby suppliers found',
                'supplier' => null,
                'products' => []
            ]);
        }

        return response()->json([
            'supplier' => $nearestSupplier->load('products'),
            'distance' => round($nearestSupplier->distance, 2) . ' km'
        ]);
    }

    /**
     * Get all suppliers within a specified radius
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'numeric|min:1|max:100'
        ]);

        $user = $request->user();
        
        // Update user's location
        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        $radius = $request->input('radius', 10); // Default 10km radius
        $suppliers = $this->locationService->getSuppliersWithinRadius($user, (float) $radius);

        return response()->json([
            'suppliers' => $suppliers->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'distance' => round($supplier->distance, 2) . ' km',
                    'products_count' => $supplier->products()->count()
                ];
            })
        ]);
    }

    /**
     * Get supplier's orders with filtering capabilities
     */
    public function orders(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('supplier_id', $request->user()->id)
            ->filterByStatus($request->status)
            ->filterByPaymentStatus($request->payment_status)
            ->filterByPaymentMethod($request->payment_method)
            ->with(['items.product', 'receipt', 'user'])
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'orders' => $orders,
        ]);
    }

    /**
     * Get supplier's specific order details
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        // Check if the order belongs to the authenticated supplier
        if ($order->supplier_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order' => $order->load(['items.product', 'receipt', 'user']),
        ]);
    }

    /**
     * Update order status (accept/reject)
     */
    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        // Check if the order belongs to the authenticated supplier
        if ($order->supplier_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);

        if ($order->status !== OrderStatus::PENDING->value) {
            return response()->json([
                'message' => 'Order cannot be updated',
                'current_status' => $order->status,
                'required_status' => OrderStatus::PENDING->value,
                'error' => 'Order must be in pending status to be accepted or rejected'
            ], 400);
        }

        $newStatus = OrderStatus::from($request->status);
        $order = $this->orderService->updateOrderStatus($order, $newStatus);

        $message = $newStatus === OrderStatus::ACCEPTED 
            ? 'Order accepted successfully' 
            : 'Order rejected successfully';

        return response()->json([
            'message' => $message,
            'order' => $order->load(['items.product', 'receipt', 'user']),
        ]);
    }

    /**
     * Update order shipping status
     */
    public function updateShippingStatus(Request $request, Order $order): JsonResponse
    {
        // Check if the order belongs to the authenticated supplier
        if ($order->supplier_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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
            'order' => $order->load(['items.product', 'receipt', 'user']),
        ]);
    }
} 