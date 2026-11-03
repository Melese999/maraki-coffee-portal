<?php

namespace App\Http\Controllers;

use App\Enums\InvestmentType;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Support\DailyProfitCalculator;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const int MaxRangeDays = 90;

    public function __invoke(Request $request, DailyProfitCalculator $calculator): View
    {
        abort_unless($request->user()?->can('dashboard.view'), 403);

        [$from, $to, $preset] = $this->resolveRange($request);

        $fromDay = $from->toDateString();
        $toDay = $to->toDateString();
        $dayCount = $from->diffInDays($to) + 1;

        $stats = $calculator->totalsForRange($from, $to);

        $previousTo = $from->copy()->subDay();
        $previousFrom = $previousTo->copy()->subDays($dayCount - 1);
        $previousStats = $calculator->totalsForRange($previousFrom, $previousTo);

        $unpaidSales = Sale::query()
            ->with(['customer', 'items'])
            ->where('payment_status', PaymentStatus::Credit)
            ->latest('sale_date')
            ->limit(6)
            ->get();

        $unpaidTotal = (float) Sale::query()
            ->where('payment_status', PaymentStatus::Credit)
            ->selectRaw('COALESCE(SUM(total - amount_paid), 0) as due')
            ->value('due');

        $openLoans = Investment::query()
            ->with('investor')
            ->where('type', InvestmentType::Loan)
            ->where('is_paid', false)
            ->latest('invested_on')
            ->limit(5)
            ->get();

        $openLoanTotal = $openLoans->sum(fn (Investment $loan) => $loan->outstanding());

        $cups = (int) SaleItem::query()
            ->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $fromDay)->whereDate('sale_date', '<=', $toDay))
            ->sum('quantity');

        $previousCups = (int) SaleItem::query()
            ->whereHas('sale', fn ($q) => $q
                ->whereDate('sale_date', '>=', $previousFrom->toDateString())
                ->whereDate('sale_date', '<=', $previousTo->toDateString()))
            ->sum('quantity');

        $salesCount = (int) Sale::query()
            ->whereDate('sale_date', '>=', $fromDay)
            ->whereDate('sale_date', '<=', $toDay)
            ->count();

        $averageTicket = $salesCount > 0
            ? round(((float) Sale::query()
                ->whereDate('sale_date', '>=', $fromDay)
                ->whereDate('sale_date', '<=', $toDay)
                ->sum('total')) / $salesCount, 2)
            : 0;

        $topItems = SaleItem::query()
            ->select('item_name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(line_total) as revenue'))
            ->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $fromDay)->whereDate('sale_date', '<=', $toDay))
            ->groupBy('item_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $topItemsMax = max(1, (int) $topItems->max('qty'));

        $expenseBreakdown = Expense::query()
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->with('category:id,name')
            ->whereDate('expense_date', '>=', $fromDay)
            ->whereDate('expense_date', '<=', $toDay)
            ->groupBy('expense_category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $expenseBreakdownMax = max(1.0, (float) $expenseBreakdown->max('total'));

        $trend = $calculator->forRange($from, $to)->map(function (array $row) {
            $cursor = Carbon::parse($row['date']);

            return [
                ...$row,
                'label' => $cursor->format($cursor->isSameMonth(now()) ? 'j' : 'M j'),
                'full_label' => $cursor->format('M j'),
            ];
        });

        $trendMax = max(1.0, (float) $trend->max(fn (array $row) => max($row['cash_collected'], $row['expenses'])));

        $dateLabel = $from->equalTo($to)
            ? $from->translatedFormat('D, M j Y')
            : $from->translatedFormat('M j Y').' – '.$to->translatedFormat('M j Y');

        return view('dashboard', [
            'from' => $fromDay,
            'to' => $toDay,
            'date' => $toDay,
            'preset' => $preset,
            'dayCount' => $dayCount,
            'dateLabel' => $dateLabel,
            'stats' => $stats,
            'deltas' => [
                'cash' => $stats['cash_collected'] - $previousStats['cash_collected'],
                'expenses' => $stats['expenses'] - $previousStats['expenses'],
                'profit' => $stats['profit'] - $previousStats['profit'],
                'cups' => $cups - $previousCups,
            ],
            'unpaidSales' => $unpaidSales,
            'unpaidTotal' => $unpaidTotal,
            'openLoans' => $openLoans,
            'openLoanTotal' => $openLoanTotal,
            'money' => Money::class,
            'cupsToday' => $cups,
            'salesCount' => $salesCount,
            'averageTicket' => $averageTicket,
            'topItems' => $topItems,
            'topItemsMax' => $topItemsMax,
            'expenseBreakdown' => $expenseBreakdown,
            'expenseBreakdownMax' => $expenseBreakdownMax,
            'trend' => $trend,
            'trendMax' => $trendMax,
            'periodTotals' => [
                'cash_collected' => $stats['cash_collected'],
                'expenses' => $stats['expenses'],
                'profit' => $stats['profit'],
            ],
            'menuItems' => MenuItem::query()->active()->ordered()->get(),
            'customers' => Customer::query()->orderBy('name')->get(),
            'categories' => ExpenseCategory::query()->active()->orderBy('name')->get(),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolveRange(Request $request): array
    {
        $preset = (string) $request->query('preset', '');

        if (in_array($preset, ['today', '7d', '30d'], true) && ! $request->filled('from') && ! $request->filled('to')) {
            $to = today();
            $from = match ($preset) {
                'today' => today(),
                '7d' => today()->subDays(6),
                '30d' => today()->subDays(29),
            };

            return [$from->startOfDay(), $to->startOfDay(), $preset];
        }

        $to = $request->date('to') ?? today();
        $from = $request->date('from') ?? $to->copy()->subDays(6);

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        if ($from->diffInDays($to) + 1 > self::MaxRangeDays) {
            $from = $to->copy()->subDays(self::MaxRangeDays - 1);
        }

        $resolvedPreset = match (true) {
            $from->equalTo(today()) && $to->equalTo(today()) => 'today',
            $from->equalTo(today()->subDays(6)) && $to->equalTo(today()) => '7d',
            $from->equalTo(today()->subDays(29)) && $to->equalTo(today()) => '30d',
            default => 'custom',
        };

        return [$from->startOfDay(), $to->startOfDay(), $resolvedPreset];
    }
}
