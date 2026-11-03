<?php

namespace App\Support;

use App\Enums\PaymentStatus;
use App\Models\CreditPayment;
use App\Models\Expense;
use App\Models\Sale;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class DailyProfitCalculator
{
    /**
     * Cash collected = paid sales recorded that day (no prior credit trail) + credit collections received that day.
     * Profit = cash collected − expenses that day.
     *
     * @return array{cash_sales: float, credit_collections: float, cash_collected: float, expenses: float, profit: float}
     */
    public function forDate(CarbonInterface $date): array
    {
        $day = $date->toDateString();

        $cashSales = (float) Sale::query()
            ->whereDate('sale_date', $day)
            ->where('payment_status', PaymentStatus::Paid)
            ->whereDoesntHave('creditPayments')
            ->sum('amount_paid');

        $creditCollections = (float) CreditPayment::query()
            ->whereDate('payment_date', $day)
            ->sum('amount');

        $expenses = (float) Expense::query()
            ->whereDate('expense_date', $day)
            ->sum('amount');

        $cashCollected = $cashSales + $creditCollections;

        return [
            'cash_sales' => $cashSales,
            'credit_collections' => $creditCollections,
            'cash_collected' => $cashCollected,
            'expenses' => $expenses,
            'profit' => $cashCollected - $expenses,
        ];
    }

    /**
     * @return array{cash_sales: float, credit_collections: float, cash_collected: float, expenses: float, profit: float}
     */
    public function totalsForRange(CarbonInterface $from, CarbonInterface $to): array
    {
        $fromDay = $from->toDateString();
        $toDay = $to->toDateString();

        $cashSales = (float) Sale::query()
            ->whereDate('sale_date', '>=', $fromDay)
            ->whereDate('sale_date', '<=', $toDay)
            ->where('payment_status', PaymentStatus::Paid)
            ->whereDoesntHave('creditPayments')
            ->sum('amount_paid');

        $creditCollections = (float) CreditPayment::query()
            ->whereDate('payment_date', '>=', $fromDay)
            ->whereDate('payment_date', '<=', $toDay)
            ->sum('amount');

        $expenses = (float) Expense::query()
            ->whereDate('expense_date', '>=', $fromDay)
            ->whereDate('expense_date', '<=', $toDay)
            ->sum('amount');

        $cashCollected = $cashSales + $creditCollections;

        return [
            'cash_sales' => $cashSales,
            'credit_collections' => $creditCollections,
            'cash_collected' => $cashCollected,
            'expenses' => $expenses,
            'profit' => $cashCollected - $expenses,
        ];
    }

    /**
     * @return Collection<int, array{date: string, cash_collected: float, expenses: float, profit: float}>
     */
    public function forRange(CarbonInterface $from, CarbonInterface $to): Collection
    {
        $rows = collect();
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $stats = $this->forDate($cursor);
            $rows->push([
                'date' => $cursor->toDateString(),
                'cash_collected' => $stats['cash_collected'],
                'expenses' => $stats['expenses'],
                'profit' => $stats['profit'],
            ]);
            $cursor->addDay();
        }

        return $rows;
    }
}
