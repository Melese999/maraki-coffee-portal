<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Http\Requests\StoreCreditPaymentRequest;
use App\Models\CreditPayment;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CreditController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('credit.view'), 403);

        $sales = Sale::query()
            ->with(['customer', 'items', 'creditPayments'])
            ->where(function ($query) {
                $query->where('payment_status', PaymentStatus::Credit)
                    ->orWhere(function ($inner) {
                        $inner->where('payment_status', PaymentStatus::Paid)
                            ->whereHas('creditPayments');
                    });
            })
            ->latest('sale_date')
            ->paginate(25);

        $openSales = Sale::query()
            ->with(['customer', 'items'])
            ->where('payment_status', PaymentStatus::Credit)
            ->orderBy('sale_date')
            ->get();

        $unpaidTotal = $openSales->sum(fn (Sale $sale) => $sale->balanceDue());

        return view('credit.index', [
            'sales' => $sales,
            'openSales' => $openSales,
            'unpaidTotal' => $unpaidTotal,
        ]);
    }

    public function store(StoreCreditPaymentRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->can('credit.create'), 403);

        DB::transaction(function () use ($request): void {
            $sale = Sale::query()->lockForUpdate()->findOrFail($request->validated('sale_id'));
            $amount = (float) $request->validated('amount');

            abort_if($amount > $sale->balanceDue() + 0.0001, 422, __('Payment exceeds balance due.'));

            CreditPayment::query()->create([
                'sale_id' => $sale->id,
                'recorded_by' => $request->user()->id,
                'payment_date' => $request->validated('payment_date'),
                'amount' => $amount,
                'note' => $request->validated('note'),
            ]);

            $sale->refreshPaymentState();
        });

        return back()->with('success', __('Credit payment recorded.'));
    }
}
