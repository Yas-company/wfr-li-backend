<?php

namespace App\Enums;

enum UserRole: string
{
    case VISITOR = 'visitor';
    case ADMIN = 'admin';
    case BUYER = 'buyer';
    case SUPPLIER = 'supplier';
    case FACTORY = 'factory';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Admin',
            self::BUYER => 'Buyer',
            self::SUPPLIER => 'Supplier',
            self::FACTORY => 'Factory',
            self::VISITOR => 'Visitor',
        };
    }

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
} 