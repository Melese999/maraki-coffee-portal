<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentRequest;
use App\Models\Investment;
use App\Models\Investor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvestmentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Investment::class);

        return view('investments.index', [
            'investments' => Investment::query()->with(['investor', 'payments'])->latest('invested_on')->paginate(20),
            'investors' => Investor::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreInvestmentRequest $request): RedirectResponse
    {
        $this->authorize('create', Investment::class);

        Investment::query()->create([
            ...$request->validated(),
            'recorded_by' => $request->user()->id,
            'is_paid' => false,
        ]);

        return back()->with('success', __('Investment / loan recorded.'));
    }

    public function show(Investment $investment): View
    {
        $this->authorize('view', $investment);

        $investment->load(['investor', 'payments.recorder']);

        return view('investments.show', compact('investment'));
    }

    public function destroy(Investment $investment): RedirectResponse
    {
        $this->authorize('delete', $investment);
        $investment->delete();

        return redirect()->route('investments.index')->with('success', __('Investment removed.'));
    }
}
