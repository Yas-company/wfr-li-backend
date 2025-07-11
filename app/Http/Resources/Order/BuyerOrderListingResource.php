<?php

namespace App\Http\Resources\Order;

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
            'products_count' => $this->products_count,
            'tracking_number' => $this->tracking_number,
            'shipping_method' => $this->shipping_method,
            'payment_status' => $this->payment_status,
        ];
    }
}
