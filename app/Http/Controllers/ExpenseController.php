<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Expense::class);

        $to = $request->date('to') ?? $request->date('date') ?? today();
        $from = $request->date('from') ?? $to->copy();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        $expenses = Expense::query()
            ->with(['category', 'recorder'])
            ->whereDate('expense_date', '>=', $from->toDateString())
            ->whereDate('expense_date', '<=', $to->toDateString())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('expenses.index', [
            'expenses' => $expenses,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'date' => $to->toDateString(),
            'categories' => ExpenseCategory::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $this->authorize('create', Expense::class);

        Expense::query()->create([
            ...$request->validated(),
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('success', __('Expense recorded.'));
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return back()->with('success', __('Expense voided.'));
    }
}
