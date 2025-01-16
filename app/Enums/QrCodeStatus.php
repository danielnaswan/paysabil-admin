<?php

namespace App\Enums;

enum QrCodeStatus: string
{
    case ACTIVE = 'ACTIVE';
    case EXPIRED = 'EXPIRED';
    case INVALID = 'INVALID';

    public static function values(): array
    {
        return array_column(self::cases(), 'values');
    }
}
