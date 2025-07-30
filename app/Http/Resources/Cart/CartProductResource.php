<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_image' => $this->product->image ? asset('storage/' . $this->product->image) : null,
            'product_price' => $this->product->price,
            'price_before_discount' => $this->product->price_before_discount,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->quantity * $this->price,
        ];
    }
}
