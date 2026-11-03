<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::query()
            ->with(['sales' => fn ($q) => $q->where('payment_status', 'credit')])
            ->orderBy('name')
            ->paginate(30);

        return view('customers.index', compact('customers'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);
        Customer::query()->create($request->validated());

        return back()->with('success', __('Customer added.'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);
        $customer->update($request->validated());

        return back()->with('success', __('Customer updated.'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);
        $customer->delete();

        return back()->with('success', __('Customer removed.'));
    }
}
