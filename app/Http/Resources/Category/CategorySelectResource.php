<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CategorySelectResource",
 *     title="Category Resource",
 *     description="Category data with field information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="object",
 *         @OA\Property(property="en", type="string", example="Fruits"),
 *         @OA\Property(property="ar", type="string", example="فواكه")
 *     ),
 * )
 */
class CategorySelectResource extends JsonResource
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
        ];
    }
}
