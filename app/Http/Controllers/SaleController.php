<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sale::class);

        $to = $request->date('to') ?? $request->date('date') ?? today();
        $from = $request->date('from') ?? $to->copy();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        $sales = Sale::query()
            ->with(['items', 'customer', 'recorder'])
            ->whereDate('sale_date', '>=', $from->toDateString())
            ->whereDate('sale_date', '<=', $to->toDateString())
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'date' => $to->toDateString(),
            'menuItems' => MenuItem::query()->active()->ordered()->get(),
            'customers' => Customer::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $this->authorize('create', Sale::class);

        $saleDate = $request->date('sale_date') ?? today();

        DB::transaction(function () use ($request, $saleDate): void {
            $lines = collect($request->validated('items'))->map(function (array $line) {
                $menuItem = MenuItem::query()->findOrFail($line['menu_item_id']);

                return [
                    'menu_item' => $menuItem,
                    'quantity' => (int) $line['quantity'],
                    'unit_price' => (float) $menuItem->price,
                    'line_total' => (float) $menuItem->price * (int) $line['quantity'],
                ];
            });

            $total = $lines->sum('line_total');
            $status = PaymentStatus::from($request->validated('payment_status'));

            $sale = Sale::query()->create([
                'recorded_by' => $request->user()->id,
                'customer_id' => $status === PaymentStatus::Credit ? $request->validated('customer_id') : null,
                'sale_date' => $saleDate->toDateString(),
                'payment_status' => $status,
                'total' => $total,
                'amount_paid' => $status === PaymentStatus::Paid ? $total : 0,
                'notes' => $request->validated('notes'),
            ]);

            foreach ($lines as $line) {
                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'menu_item_id' => $line['menu_item']->id,
                    'item_name' => $line['menu_item']->name,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);
            }
        });

        $from = $request->date('filter_from') ?? $saleDate->copy();
        $to = $request->date('filter_to') ?? $saleDate->copy();

        if ($saleDate->lt($from)) {
            $from = $saleDate->copy();
        }

        if ($saleDate->gt($to)) {
            $to = $saleDate->copy();
        }

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        return redirect()
            ->route('sales.index', [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ])
            ->with('success', __('Sale recorded.'));
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->authorize('delete', $sale);
        $sale->delete();

        return back()->with('success', __('Sale voided.'));
    }
}
