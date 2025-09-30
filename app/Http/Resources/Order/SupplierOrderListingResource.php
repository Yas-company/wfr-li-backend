<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="SupplierOrderListing",
 *     title="Supplier Order Listing Resource",
 *     description="Order listing information for suppliers",
 *     @OA\Property(property="id", type="integer", example=111, description="Order ID"),
 *     @OA\Property(
 *         property="status",
 *         type="object",
 *         @OA\Property(property="value", type="string", example="shipped", description="Order status value"),
 *         @OA\Property(property="label", type="string", example="مشحون", description="Order status label in Arabic")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-10T16:24:46.000000Z", description="Order creation date"),
 *     @OA\Property(property="buyer_name", type="string", example="buyer-1", description="Name of the buyer"),
 *     @OA\Property(property="products_count", type="integer", example=4, description="Number of products in the order"),
 *     @OA\Property(property="tracking_number", type="string", example="640664111", description="Shipping tracking number"),
 *     @OA\Property(property="shipping_method", type="integer", example=2, description="Shipping method ID"),
 *     @OA\Property(property="payment_status", type="string", example="3", description="Payment status code"),
 *     @OA\Property(property="order_type", type="string", example="1", description="Type of order")
 * )
 */
class SupplierOrderListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->toResponse(),
            'created_at' => $this->created_at,
            'buyer_name' => $this->buyer_name,
            'products_count' => $this->products_count,
            'currency' => $this->currency,
            'tracking_number' => $this->tracking_number,
            'shipping_method' => $this->shipping_method,
            'payment_status' => $this->payment_status,
            'order_type' => $this->order_type,
        ];
    }
}
