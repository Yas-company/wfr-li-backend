<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=111),
 *     @OA\Property(property="user_id", type="integer", example=669),
 *     @OA\Property(
 *         property="status",
 *         type="object",
 *         @OA\Property(property="value", type="string", example="shipped"),
 *         @OA\Property(property="label", type="string", example="مشحون")
 *     ),
 *     @OA\Property(property="total", type="string", example="1445.00"),
 *     @OA\Property(property="total_discount", type="string", example="200.63"),
 *     @OA\Property(property="order_type", type="string", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-10T16:24:46.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-10T16:24:46.000000Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
 *     @OA\Property(
 *         property="orderDetail",
 *         type="object",
 *         @OA\Property(property="notes", type="string", example="Order notes here"),
 *         @OA\Property(property="payment_status", type="string", example="3"),
 *         @OA\Property(property="payment_method", type="string", example="2"),
 *         @OA\Property(property="tracking_number", type="string", example="640664111"),
 *         @OA\Property(property="estimated_delivery_date", type="string", example="2025-08-12"),
 *         @OA\Property(
 *             property="shipping_address",
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=160),
 *             @OA\Property(property="name", type="string", example="Shipping Address Name"),
 *             @OA\Property(property="street", type="string", example="55120 Pollich Highway"),
 *             @OA\Property(property="city", type="string", example="makkah"),
 *             @OA\Property(property="phone", type="string", example="966595924198"),
 *             @OA\Property(property="latitude", type="string", example="42.70227900"),
 *             @OA\Property(property="longitude", type="string", example="-35.14541500"),
 *             @OA\Property(property="is_default", type="boolean", example=true),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         ),
 *         @OA\Property(property="shipping_method", type="integer", example=2)
 *     ),
 *     @OA\Property(property="supplier", type="object", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="products_count", type="integer", example=4),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=317),
 *             @OA\Property(property="name", type="string", example="خبز طازج"),
 *             @OA\Property(property="image", type="string", example="/images/logo.jpg"),
 *             @OA\Property(property="price", type="string", example="200.00"),
 *             @OA\Property(property="price_before_discount", type="string", example="228.00"),
 *             @OA\Property(property="order_price", type="string", example="200.00"),
 *             @OA\Property(property="order_quantity", type="integer", example=1),
 *             @OA\Property(property="order_total", type="integer", example=200),
 *             @OA\Property(property="stock_qty", type="integer", example=989),
 *             @OA\Property(
 *                 property="category",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=198),
 *                 @OA\Property(property="name", type="string", example="غسالات"),
 *                 @OA\Property(property="image", type="string", nullable=true, example=null)
 *             )
 *         )
 *     ),
 * )
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status->toResponse(),
            'total' => $this->total,
            'total_discount' => $this->total_discount,
            'order_type' => $this->order_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'receipt' => $this->whenLoaded('receipt'),
            'user' => new UserResource($this->whenLoaded('user')),
            'orderDetail' => OrderDetailResource::make($this->whenLoaded('orderDetail')),
            'supplier' => UserResource::make($this->whenLoaded('supplier')),
            'products_count' => $this->products_count,
            'products' => $this->whenLoaded('products', function () {
                return $this->products->map(function ($item) {
                    $product = $item->product;
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image' => $product->getFirstMediaUrl('images'),
                        'price' => $product->price,
                        'price_before_discount' => $product->price_before_discount,
                        'order_price' => $item->price,
                        'order_quantity' => $item->quantity,
                        'order_total' => ($item->price * $item->quantity),
                        'stock_qty' => $product->stock_qty,
                        'category' => [
                            'id' => $product->category->id,
                            'name' => $product->category->name,
                            'image' => $product->category->image_url,
                        ]
                    ];
                });
            }),
        ];
    }
}
