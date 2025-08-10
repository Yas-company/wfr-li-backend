<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrganizationResource",
 *     title="Organization Resource",
 *     description="Organization information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ABC Company Ltd"),
 *     @OA\Property(property="tax_number", type="string", example="1234567890"),
 *     @OA\Property(property="commercial_register_number", type="string", example="1234567890"),
 *     @OA\Property(property="status", type="string", example="Active"),
 *     @OA\Property(property="owner", ref="#/components/schemas/OrganizationUserResource"),
 *     @OA\Property(property="members", type="array", @OA\Items(ref="#/components/schemas/OrganizationUserResource"))
 * )
 */
class OrganizationResource extends JsonResource
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
            'tax_number' => $this->tax_number,
            'commercial_register_number' => $this->commercial_register_number,
            'status' => $this->status->label(),
            'owner' => OrganizationUserResource::make($this->whenLoaded('owner')),
            'members' => OrganizationUserResource::collection($this->whenLoaded('users')),
        ];
    }
}
