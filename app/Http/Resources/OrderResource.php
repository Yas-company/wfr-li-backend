<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name ?? null,
            ],
            'total' => $this->total,
            'payment_method' => $this->paymentMethod?->name_en,
            'status' => $this->status,
            'notes' => $this->notes,
            'items' => OrderItemResource::collection($this->items),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
