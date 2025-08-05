<?php

namespace App\Enums\Organization;

enum OrganizationStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;

    public function label(): string
    {
        return match($this) {
            self::PENDING => __('enums.organization_status.pending'),
            self::APPROVED => __('enums.organization_status.approved'),
            self::REJECTED => __('enums.organization_status.rejected'),
        };
    }

    public static function colors(): array
    {
        return [
            'gray' => self::PENDING->value,
            'success' => self::APPROVED->value,
            'danger' => self::REJECTED->value,
        ];
    }
}
