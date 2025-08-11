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
 *     @OA\Property(property="id", type="integer", example=669),
 *     @OA\Property(property="name", type="string", example="buyer-1"),
 *     @OA\Property(property="email", type="string", example="buyer1@wfrli.com"),
 *     @OA\Property(property="phone", type="string", example="0512345671"),
 *     @OA\Property(property="image", type="string", nullable=true, example=null),
 *     @OA\Property(
 *         property="role",
 *         type="object",
 *         @OA\Property(property="value", type="string", example="buyer"),
 *         @OA\Property(property="label", type="string", example="مشتري")
 *     ),
 *     @OA\Property(property="is_verified", type="boolean", example=true),
 *     @OA\Property(property="business_name", type="string", example="Trading"),
 *     @OA\Property(property="license_attachment", type="string", nullable=true, example=null),
 *     @OA\Property(property="commercial_register_attachment", type="string", nullable=true, example=null),
 *     @OA\Property(
 *         property="status",
 *         type="object",
 *         @OA\Property(property="value", type="string", example="approved"),
 *         @OA\Property(property="label", type="string", example="موافق")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-10T16:27:34.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-10T16:27:34.000000Z"),
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
