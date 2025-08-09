<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="MediaResource",
 *     title="Media Resource",
 *     description="Media/Image data with different sizes",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="product-image.jpg"),
 *     @OA\Property(property="original", type="string", example="https://example.com/original/product-image.jpg"),
 *     @OA\Property(property="thumb", type="string", example="https://example.com/thumb/product-image.jpg"),
 *     @OA\Property(property="preview", type="string", example="https://example.com/preview/product-image.jpg")
 * )
 */
class MediaResource extends JsonResource
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
            'original' => $this->getUrl(),
            'thumb' => $this->getUrl('thumb'),
            'preview' => $this->getUrl('preview'),
        ];
    }
}
