<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     title="Category Resource",
 *     description="Category data with field information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="object",
 *         @OA\Property(property="en", type="string", example="Fruits"),
 *         @OA\Property(property="ar", type="string", example="فواكه")
 *     ),
 *     @OA\Property(property="image", type="string", example="https://example.com/category-image.jpg"),
 *     @OA\Property(property="field_id", type="integer", example=1),
 *     @OA\Property(property="products_count", type="integer", example=25),
 *     @OA\Property(property="field", ref="#/components/schemas/FieldResource")
 * )
 */
class CategoryResource extends JsonResource
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
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'field_id' => $this->field_id,
            'products_count' => $this->products_count,
            'field' => new FieldResource($this->whenLoaded('field')),
            'products' => ProductResource::collection($this->whenLoaded('products'))
        ];
    }
}
