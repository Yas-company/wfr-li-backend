<?php

namespace App\Enums;

use App\Traits\HasLabel;

enum UserRole: string
{
    use HasLabel;

    case VISITOR = 'visitor';
    case ADMIN = 'admin';
    case BUYER = 'buyer';
    case SUPPLIER = 'supplier';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isSupplier(): bool
    {
        return $this === self::SUPPLIER;
    }
}
