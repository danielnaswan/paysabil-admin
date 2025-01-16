<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = "PENDING";
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
    case CANCELLED = 'CANCELLED';

    public function values(): array
    {
        return array_column(self::cases(),'values');
    }
}
