<?php

namespace App\Enums;

use App\Traits\HasLabel;

enum UserStatus: string
{
    use HasLabel;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    // public function label(): string
    // {
    //     return match($this) {
    //         self::PENDING => 'Pending',
    //         self::APPROVED => 'Approved',
    //         self::REJECTED => 'Rejected',
    //     };
    // }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }
} 