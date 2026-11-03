<?php

namespace App\Http\Controllers;

use App\Enums\InvestmentType;
use App\Http\Requests\StoreLoanPaymentRequest;
use App\Models\Investment;
use App\Models\LoanPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LoanPaymentController extends Controller
{
    public function store(StoreLoanPaymentRequest $request, Investment $investment): RedirectResponse
    {
        $this->authorize('update', $investment);
        abort_unless($investment->type === InvestmentType::Loan, 422);

        DB::transaction(function () use ($request, $investment): void {
            $amount = (float) $request->validated('amount');
            abort_if($amount > $investment->outstanding() + 0.0001, 422, __('Payment exceeds outstanding loan.'));

            LoanPayment::query()->create([
                'investment_id' => $investment->id,
                'recorded_by' => $request->user()->id,
                'payment_date' => $request->validated('payment_date'),
                'amount' => $amount,
                'note' => $request->validated('note'),
            ]);

            $investment->refreshPaidState();
        });

        return back()->with('success', __('Loan payment recorded.'));
    }
}
