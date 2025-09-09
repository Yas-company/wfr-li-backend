<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\RatingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="BuyerOrderListing",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="supplier_name", type="string"),
 *     @OA\Property(property="supplier_image", type="string"),
 *     @OA\Property(property="products_count", type="integer"),
 *     @OA\Property(property="total", type="number", format="float"),
 *     @OA\Property(property="total", type="number", format="float"),
 *     @OA\Property(property="total_discount", type="number", format="float"),
 *     @OA\Property(property="tracking_number", type="string"),
 *     @OA\Property(property="shipping_method", type="string"),
 *     @OA\Property(property="payment_status", type="string"),
 *     @OA\Property(property="order_type", type="string"),
 *     @OA\Property(property="ratings", type="array", @OA\Items(ref="#/components/schemas/RatingResource")),
 * )
 */
class BuyerOrderListingResource extends JsonResource
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
            'supplier_name' => $this->supplier_name,
            'supplier_image' => $this->supplier_image ? asset('storage/'.$this->supplier_image) : null,
            'products_count' => $this->products_count,
            'total' => $this->total,
            'total_products' => $this->total_products,
            'total_discount' => $this->total_discount,
            'tracking_number' => $this->tracking_number,
            'shipping_method' => $this->shipping_method,
            'payment_status' => $this->payment_status,
            'order_type' => $this->order_type,
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}
