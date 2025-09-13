<?php

namespace App\Http\Resources;

use App\Enums\Settings\OrderSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierSettingResource extends JsonResource
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
            'user_id' => $this->user_id,
            'key' => $this->key,
            'key_label' => $this->getKeyLabel(),
            'value' => $this->value,
        ];
    }

    private function getKeyLabel(): ?string
    {
        $orderSettings = collect(OrderSettings::cases())->first(fn ($setting) => $setting->value === $this->key);

        return $orderSettings?->label();
    }
}
