<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'owner' => OrganizationUserResource::make($this->whenLoaded('owner')),
            'members' => OrganizationUserResource::collection($this->whenLoaded('users')),
        ];
    }
}
