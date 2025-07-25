<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\RatingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'supplier_name' => $this->supplier_name,
            'supplier_image' => $this->supplier_image ? asset('storage/'.$this->supplier_image) : null,
            'products_count' => $this->products_count,
            'total' => $this->total,
            'total_discount' => $this->total_discount,
            'tracking_number' => $this->tracking_number,
            'shipping_method' => $this->shipping_method,
            'payment_status' => $this->payment_status,
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}
