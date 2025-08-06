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
            'status' => $this->status->label(),
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
