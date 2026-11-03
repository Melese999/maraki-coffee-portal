<?php

namespace App\Models;

use App\Enums\InvestmentType;
use Database\Factories\InvestmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['investor_id', 'recorded_by', 'type', 'amount', 'invested_on', 'is_paid', 'notes'])]
class Investment extends Model
{
    /** @use HasFactory<InvestmentFactory> */
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
            'type' => InvestmentType::class,
            'amount' => 'decimal:2',
            'invested_on' => 'date',
            'is_paid' => 'boolean',
        ];
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function amountRepaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function outstanding(): float
    {
        if ($this->type === InvestmentType::Equity) {
            return 0;
        }

        return max(0, (float) $this->amount - $this->amountRepaid());
    }

    public function refreshPaidState(): void
    {
        if ($this->type === InvestmentType::Equity) {
            return;
        }

        $this->forceFill([
            'is_paid' => $this->outstanding() <= 0.00001,
        ])->save();
    }
}
