<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="SupplierResource",
 *     title="Supplier Resource",
 *     description="Supplier information with fields and rating",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ABC Company Ltd"),
 *     @OA\Property(property="image", type="string", example="https://example.com/supplier-image.jpg"),
 *     @OA\Property(property="rating", type="number", format="float", example=4.7),
 *     @OA\Property(property="fields", type="array", @OA\Items(ref="#/components/schemas/FieldResource")),
 *     @OA\Property(property="supplier_status", type="boolean", example=true)
 * )
 */
class SupplierResource extends JsonResource
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
            'fields' => FieldResource::collection($this->whenLoaded('fields')),
            'supplier_status' => $this->supplier?->status ?? true,
        ];
    }
}
