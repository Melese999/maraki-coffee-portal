<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Confirmed => __('Confirmed'),
            self::Preparing => __('Preparing'),
            self::Ready => __('Ready'),
            self::Completed => __('Completed'),
            self::Cancelled => __('Cancelled'),
        };
    }

    /**
     * @return list<self>
     */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Preparing, self::Cancelled],
            self::Preparing => [self::Ready, self::Cancelled],
            self::Ready => [self::Completed, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }
}
