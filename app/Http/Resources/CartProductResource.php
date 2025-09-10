<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CartProduct",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=317),
 *     @OA\Property(property="product_name", type="string", example="Fresh Bread"),
 *     @OA\Property(property="product_image", type="string", example="/images/logo.jpg"),
 *     @OA\Property(property="product_price", type="number", format="float", example=200.00),
 *     @OA\Property(property="price_before_discount", type="number", format="float", example=228.00),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="float", example=150.00),
 *     @OA\Property(property="total", type="number", format="float", example=300.00)
 * )
 */
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
            'product_image' => $this->product->getFirstMediaUrl('images'),
            'product_price' => $this->product->price,
            'price_before_discount' => $this->product->price_before_discount,
            'price_after_taxes' => $this->product->price_after_taxes,
            'country_tax' => $this->product->country_tax,
            'discount_rate' => to_base($this->product->discount_rate),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => money($this->quantity * $this->price, 2),
            'total_after_taxes' => money($this->quantity * $this->product->price_after_taxes, 2),
            'total_country_tax' => money($this->quantity * $this->product->country_tax, 2),
        ];
    }
}
