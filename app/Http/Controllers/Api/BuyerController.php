<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            ->with(['receipt', 'supplier'])
            ->latest()
            ->paginate(10);

        $transformedOrders = $orders->through(function ($order) {
            return $this->transformOrderData($order);
        });

        return response()->json($transformedOrders);
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
            $this->transformOrderData($order->load(['items.product.category', 'receipt', 'supplier']))
        );
    }

    /**
     * Transform order data with specific product fields
     */
    private function transformOrderData(Order $order)
    {
        $orderData = $order->toArray();
        $orderData['items'] = $order->items->map(function ($item) {
            $product = $item->product;
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->image_url,
                'price' => $product->price,
                'price_before_discount' => $product->price_before_discount,
                'cart_quantity' => $item->quantity,
                'total' => $item->total,
                'stock_qty' => $product->stock_qty,
                'is_favorite' => $product->is_favorite,
                'category' => [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'image' => $product->category->image_url,
                ]
            ];
        })->toArray();
        
        return $orderData;
    }
} 