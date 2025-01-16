<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case VENDOR = 'VENDOR';
    case STUDENT = 'STUDENT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}