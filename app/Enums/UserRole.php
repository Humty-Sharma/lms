<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'superadmin';
    case Admin = 'admin';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'SuperAdmin',
            self::Admin => 'Admin',
            self::Member => 'Member',
        };
    }

    public static function assignableByAdmin(): array
    {
        return [
            self::Admin->value,
            self::Member->value,
        ];
    }
}

