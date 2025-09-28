<?php

namespace App\Http\Resources;

use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="SupplierDetailsResource",
 *     title="Supplier Details Resource",
 *     description="Detailed supplier information with fields, categories, and rating",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ABC Company Ltd"),
 *     @OA\Property(property="image", type="string", example="https://example.com/supplier-image.jpg"),
 *     @OA\Property(property="rating", type="number", format="float", example=4.7),
 *     @OA\Property(property="fields", type="array", @OA\Items(ref="#/components/schemas/FieldResource")),
 *     @OA\Property(property="supplier_status", type="boolean", example=true)
 * )
 */
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
            'categories' => CategoryResource::collection($this->whenLoaded('fields', fn () => $this->fields->flatMap->categories->values(), collect())),
            'supplier_status' => $this->supplier->is_open,
        ];
    }
}
