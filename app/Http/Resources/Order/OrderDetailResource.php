<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'notes' => $this->notes,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'tracking_number' => $this->tracking_number,
            'estimated_delivery_date' => $this->estimated_delivery_date,
            'shipping_address' => AddressResource::make($this->whenLoaded('shippingAddress')),
            'shipping_method' => $this->shipping_method,
        ];
    }
}
