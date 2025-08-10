<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Organization\OrganizationResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     title="User Resource",
 *     description="User data with profile information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="966555555555"),
 *     @OA\Property(property="image", type="string", example="https://example.com/user-image.jpg"),
 *     @OA\Property(property="role", type="string", example="Buyer"),
 *     @OA\Property(property="is_verified", type="boolean", example=true),
 *     @OA\Property(property="business_name", type="string", example="ABC Company Ltd"),
 *     @OA\Property(property="license_attachment", type="string", example="https://example.com/license.pdf"),
 *     @OA\Property(property="commercial_register_attachment", type="string", example="https://example.com/register.pdf"),
 *     @OA\Property(property="status", type="string", example="Approved"),
 *     @OA\Property(property="fields", type="array", @OA\Items(ref="#/components/schemas/FieldResource")),
 *     @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/CategoryResource")),
 *     @OA\Property(property="addresses", type="array", @OA\Items(ref="#/components/schemas/AddressResource")),
 *     @OA\Property(property="organization", ref="#/components/schemas/OrganizationResource"),
 *     @OA\Property(property="organizations", type="array", @OA\Items(ref="#/components/schemas/OrganizationResource")),
 *     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
 *     @OA\Property(property="is_organization", type="boolean", example=false)
 * )
 */
class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'role' => $this->role->toResponse(),
            'is_verified' => $this->is_verified,
            'business_name' => $this->business_name,
            'license_attachment' => $this->license_attachment,
            'commercial_register_attachment' => $this->commercial_register_attachment,
            'status' => $this->status->toResponse(),
            'fields' => FieldResource::collection($this->whenLoaded('fields')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'organizations' => OrganizationResource::collection($this->whenLoaded('organizations')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_organization' => $this->isOrganization(),
        ];
    }
}
