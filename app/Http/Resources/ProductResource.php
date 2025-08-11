<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     title="Product Resource",
 *     description="Product data with media, ratings, and category information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="object",
 *         @OA\Property(property="en", type="string", example="Fresh Apples"),
 *         @OA\Property(property="ar", type="string", example="تفاح طازج")
 *     ),
 *     @OA\Property(property="description", type="object",
 *         @OA\Property(property="en", type="string", example="Fresh red apples from local farms"),
 *         @OA\Property(property="ar", type="string", example="تفاح أحمر طازج من المزارع المحلية")
 *     ),
 *     @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/MediaResource")),
 *     @OA\Property(property="image", type="string", example="https://example.com/product-image.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=25.50),
 *     @OA\Property(property="price_before_discount", type="number", format="float", example=30.00),
 *     @OA\Property(property="quantity", type="integer", example=100),
 *     @OA\Property(property="stock_qty", type="integer", example=50),
 *     @OA\Property(property="nearly_out_of_stock_limit", type="integer", example=10),
 *     @OA\Property(property="status", type="string", example="Published"),
 *     @OA\Property(property="is_favorite", type="boolean", example=false),
 *     @OA\Property(property="unit_type", type="string", example="Kilogram"),
 *     @OA\Property(property="category", ref="#/components/schemas/CategoryResource"),
 *     @OA\Property(property="ratings", type="array", @OA\Items(ref="#/components/schemas/RatingResource"))
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'images' => $this->relationLoaded('media') ? MediaResource::collection($this->getMedia('images')) : [],
            'image' => $this->relationLoaded('media') ? $this->getFirstMediaUrl('images') : null,
            'price' => $this->price,
            'price_before_discount' => $this->price_before_discount,
            'quantity' => $this->quantity,
            'stock_qty' => $this->stock_qty,
            'nearly_out_of_stock_limit' => $this->nearly_out_of_stock_limit,
            'status' => $this->status->toResponse(),
            'is_favorite' => $this->is_favorite,
            'unit_type' => $this->unit_type->toResponse(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
        ];
    }
}
