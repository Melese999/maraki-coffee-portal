<?php

namespace App\Models;

use App\Enums\FulfillmentType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'order_number',
    'customer_name',
    'customer_phone',
    'fulfillment_type',
    'delivery_address',
    'notes',
    'status',
    'payment_status',
    'payment_method',
    'subtotal',
    'total',
    'sale_id',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'fulfillment_type' => FulfillmentType::class,
            'status' => OrderStatus::class,
            'payment_status' => OrderPaymentStatus::class,
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'MK-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (self::query()->where('order_number', $number)->exists());

        return $number;
    }

    public function isOpen(): bool
    {
        return ! in_array($this->status, [OrderStatus::Completed, OrderStatus::Cancelled], true);
    }

    public function canConvertToSale(): bool
    {
        return $this->sale_id === null
            && $this->status !== OrderStatus::Cancelled
            && $this->items()->exists();
    }
}
