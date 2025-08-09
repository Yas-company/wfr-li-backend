<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="FieldResource",
 *     title="Field Resource",
 *     description="Field/Industry data",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="object",
 *         @OA\Property(property="en", type="string", example="Agriculture"),
 *         @OA\Property(property="ar", type="string", example="زراعة")
 *     ),
 *     @OA\Property(property="image", type="string", example="https://example.com/field-image.jpg")
 * )
 */
class FieldResource extends JsonResource
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
            'image' => $this->image ? asset('storage/' . $this->image) : null
        ];
    }
} 