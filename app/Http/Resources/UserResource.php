<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'role' => $this->role,
            'is_verified' => $this->is_verified,
            'business_name' => $this->business_name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'license_attachment' => $this->license_attachment,
            'commercial_register_attachment' => $this->commercial_register_attachment,
            'status' => $this->status,
            'fields' => FieldResource::collection($this->whenLoaded('fields')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 