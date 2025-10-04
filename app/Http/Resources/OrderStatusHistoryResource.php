<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'old_status' => $this->old_status,
            'new_status' => $this->new_status,
            'changed_by' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'image' => $this->user->image ? asset('storage/' . $this->user->image) : null,
                'role' => $this->user->role,
            ]: null,
            'created_at' => $this->created_at,
        ];
    }
}
