<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AddressResource",
 *     title="Address Resource",
 *     description="Address information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Home Address"),
 *     @OA\Property(property="street", type="string", example="123 Main Street"),
 *     @OA\Property(property="city", type="string", example="Riyadh"),
 *     @OA\Property(property="phone", type="string", example="966555555555"),
 *     @OA\Property(property="latitude", type="number", format="float", example=24.7136),
 *     @OA\Property(property="longitude", type="number", format="float", example=46.6753),
 *     @OA\Property(property="is_default", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00")
 * )
 */
class AddressResource extends JsonResource
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
            'street' => $this->street,
            'city' => $this->city,
            'phone' => $this->phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
