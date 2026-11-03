<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Credit = 'credit';

    public function label(): string
    {
        return match ($this) {
            self::Paid => __('Paid'),
            self::Credit => __('Credit'),
        };
    }
}
