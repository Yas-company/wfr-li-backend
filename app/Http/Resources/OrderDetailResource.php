<?php

namespace App\Http\Resources;

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
            'shipping_address' => $this->shipping_address,
            'shipping_latitude' => $this->shipping_latitude,
            'shipping_longitude' => $this->shipping_longitude,
            'notes' => $this->notes,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'tracking_number' => $this->tracking_number,
            'estimated_delivery_date' => $this->estimated_delivery_date,
        ];
    }
}
