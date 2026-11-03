<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['recorded_by', 'customer_id', 'sale_date', 'payment_status', 'total', 'amount_paid', 'notes'])]
class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'payment_status' => PaymentStatus::class,
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function creditPayments(): HasMany
    {
        return $this->hasMany(CreditPayment::class);
    }

    public function balanceDue(): float
    {
        return max(0, (float) $this->total - (float) $this->amount_paid);
    }

    public function isFullyPaid(): bool
    {
        return $this->balanceDue() <= 0.00001;
    }

    public function refreshPaymentState(): void
    {
        $collected = (float) $this->creditPayments()->sum('amount');

        if ($this->payment_status === PaymentStatus::Paid) {
            $this->forceFill(['amount_paid' => $this->total])->save();

            return;
        }

        $amountPaid = min((float) $this->total, $collected);
        $status = $amountPaid >= (float) $this->total
            ? PaymentStatus::Paid
            : PaymentStatus::Credit;

        $this->forceFill([
            'amount_paid' => $amountPaid,
            'payment_status' => $status,
        ])->save();
    }
}
