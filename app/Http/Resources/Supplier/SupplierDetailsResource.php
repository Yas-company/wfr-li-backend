<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Request;
use App\Http\Resources\FieldResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDetailsResource extends JsonResource
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
            'name' => $this->name,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'rating' => 4.7,
            'fields' => FieldResource::collection($this->whenLoaded('fields')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'supplier_status' => $this->supplier->status,
        ];
    }
}
