<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
class FavoriteProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @OA\Schema(
     *     schema="FavoriteProductResource",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="product_id", type="integer", example=1),
     *     @OA\Property(property="is_favorite", type="boolean", example=true),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00Z")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'is_favorite' => $this->is_favorite,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
