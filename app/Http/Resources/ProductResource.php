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
            'image' => $this->image_url,
            'price_before_discount' => $this->price_before_discount,
            'price' => $this->price,
            'description' => $this->description,
            'stock_qty' => $this->stock_qty,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'is_favorite' => $request->user() ? $this->favoritedByUsers()->where('user_id', $request->user()->id)->exists() : false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 