<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case CLIENT = 'client';

    public static function values(): array
    {
        return array_column(UserType::cases(), 'value');
    }
}
