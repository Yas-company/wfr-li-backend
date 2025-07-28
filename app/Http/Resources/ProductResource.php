<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'price' => $this->price,
            'price_before_discount' => $this->price_before_discount,
            'quantity' => $this->quantity,
            'stock_qty' => $this->stock_qty,
            'nearly_out_of_stock_limit' => $this->nearly_out_of_stock_limit,
            'status' => $this->status,
            'is_favorite' => $this->is_favorite,
            'unit_type' => $this->unit_type,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
            'avg_rating' => $this->averageRating(),
        ];
    }

}
