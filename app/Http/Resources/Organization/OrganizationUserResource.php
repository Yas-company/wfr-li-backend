<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrganizationUserResource",
 *     title="Organization User Resource",
 *     description="User information within organization context",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="phone", type="string", example="966555555555"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="image", type="string", example="https://example.com/user-image.jpg"),
 *     @OA\Property(property="role", type="string", example="Admin"),
 *     @OA\Property(property="joined_at", type="string", example="2021-01-01 00:00:00")
 * )
 */
class OrganizationUserResource extends JsonResource
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
            'phone' => $this->phone,
            'email' => $this->email,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'role' => $this->pivot?->role,
            'joined_at' => $this->pivot?->joined_at,
        ];
    }
}
