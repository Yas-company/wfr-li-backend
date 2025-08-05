<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Organization\OrganizationResource;

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
            'role' => $this->role,
            'is_verified' => $this->is_verified,
            'business_name' => $this->business_name,
            'license_attachment' => $this->license_attachment,
            'commercial_register_attachment' => $this->commercial_register_attachment,
            'status' => $this->status,
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
