<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestorRequest;
use App\Models\Investor;
use Illuminate\Http\RedirectResponse;

class InvestorController extends Controller
{
    public function store(StoreInvestorRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->can('investments.create'), 403);

        Investor::query()->create($request->validated());

        return back()->with('success', __('Investor / lender added.'));
    }
}
