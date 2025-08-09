<?php

namespace App\Http\Resources\Buyer;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="HomePageBuyerResource",
 *     title="Home Page Buyer Resource",
 *     description="Supplier data with products for buyer home page",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ABC Company Ltd"),
 *     @OA\Property(property="image", type="string", example="https://example.com/supplier-image.jpg"),
 *     @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
 * )
 */
class HomePageBuyerResource extends JsonResource
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
            'image' => $this->image,
            'products' => ProductResource::collection($this->products),
        ];
    }
}
