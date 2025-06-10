<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'supplier_id' => $this->supplier_id,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'shipping_address' => $this->shipping_address,
            'shipping_latitude' => $this->shipping_latitude,
            'shipping_longitude' => $this->shipping_longitude,
            'notes' => $this->notes,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'payment_id' => $this->payment_id,
            'tracking_number' => $this->tracking_number,
            'estimated_delivery_date' => $this->estimated_delivery_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'receipt' => $this->whenLoaded('receipt'),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
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
                });
            }),
        ];
    }
} 