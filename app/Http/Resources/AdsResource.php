<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AdsResource",
 *     title="Ads Resource",
 *     description="Advertisement data",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Special Offer"),
 *     @OA\Property(property="description", type="string", example="Get 20% off on all products"),
 *     @OA\Property(property="image", type="string", example="https://example.com/ad-image.jpg"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class AdsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'user_id' => $this->user_id,
            'is_active' => $this->is_active
        ];
    }
}
