<?php

namespace App\Enums;

enum OrderPaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Refunded = 'refunded';
    case PendingOnline = 'pending_online';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => __('Unpaid'),
            self::Paid => __('Paid'),
            self::Refunded => __('Refunded'),
            self::PendingOnline => __('Pending online'),
        };
    }
}
