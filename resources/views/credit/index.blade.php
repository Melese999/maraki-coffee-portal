<x-app-layout>
    <x-slot name="header">{{ __('Credit') }}</x-slot>

    <div class="mb-5 grid gap-4 sm:grid-cols-2">
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Open credit total') }}</div>
            <div class="mt-2 font-display text-3xl text-terracotta">{{ \App\Support\Money::format($unpaidTotal) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Open tickets') }}</div>
            <div class="mt-2 font-display text-3xl">{{ $openSales->count() }}</div>
        </div>
    </div>

    <div class="mb-5 flex flex-wrap justify-between gap-3">
        @can('customers.view')
            <a href="{{ route('customers.index') }}" class="btn-secondary">{{ __('Customers') }}</a>
        @endcan
        @can('credit.create')
            <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'collect-credit')" @disabled($openSales->isEmpty())>
                {{ __('Collect payment') }}
            </button>
        @endcan
    </div>

    <section class="panel overflow-hidden">
        <div class="hidden overflow-x-auto md:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Items') }}</th>
                        <th>{{ __('Balance') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $sale->customer?->name ?? '—' }}</div>
                                <div class="text-xs text-bean dark:text-parchment/50">{{ $sale->sale_date->format('M j, Y') }}</div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($sale->items as $item)
                                        <span class="badge-soft">{{ $item->quantity }}× {{ $item->item_name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="font-semibold">{{ \App\Support\Money::format($sale->balanceDue()) }}</td>
                            <td>
                                <span @class([$sale->payment_status->value === 'paid' ? 'badge-paid' : 'badge-credit'])>
                                    {{ $sale->payment_status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-bean dark:text-parchment/55">{{ __('No credit activity yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-4 md:hidden">
            @forelse ($sales as $sale)
                <article class="rounded-2xl border border-bean/10 bg-parchment-soft/60 p-4 dark:border-white/5 dark:bg-obsidian-soft">
                    <div class="flex justify-between gap-3">
                        <div>
                            <div class="font-semibold">{{ $sale->customer?->name ?? '—' }}</div>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach ($sale->items as $item)
                                    <span class="badge-soft">{{ $item->quantity }}× {{ $item->item_name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold">{{ \App\Support\Money::format($sale->balanceDue()) }}</div>
                            <span @class(['mt-1 inline-flex', $sale->payment_status->value === 'paid' ? 'badge-paid' : 'badge-credit'])>
                                {{ $sale->payment_status->label() }}
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                <p class="py-8 text-center text-sm text-bean dark:text-parchment/55">{{ __('No credit activity yet.') }}</p>
            @endforelse
        </div>

        <div class="border-t border-bean/10 p-4 dark:border-white/5">{{ $sales->links() }}</div>
    </section>

    <div class="modals-root">
        @can('credit.create')
            <x-ui.dialog
                name="collect-credit"
                :title="__('Collect payment')"
                :subtitle="__('Apply cash against an open ticket.')"
                maxWidth="xl"
                :show="$errors->any() && old('_form') === 'credit'"
            >
                <form method="POST" action="{{ route('credit.payments.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="credit">
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('Unpaid sale') }}</h3>
                        <select class="field" name="sale_id" id="sale_id" required>
                            @foreach ($openSales as $sale)
                                <option value="{{ $sale->id }}" @selected(old('sale_id') == $sale->id)>
                                    {{ $sale->customer?->name ?? __('Customer') }} — {{ $sale->items->pluck('item_name')->join(', ') }} — {{ __('due :amount', ['amount' => \App\Support\Money::format($sale->balanceDue())]) }}
                                </option>
                            @endforeach
                        </select>
                    </section>
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('Payment') }}</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label" for="payment_date">{{ __('Date') }}</label>
                                <input class="field" type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required>
                            </div>
                            <div>
                                <label class="label" for="amount">{{ __('Amount') }}</label>
                                <input class="field" type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount') }}" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="label" for="note">{{ __('Note') }}</label>
                            <input class="field" type="text" name="note" id="note" value="{{ old('note') }}">
                        </div>
                    </section>
                    <div class="dialog-actions">
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'collect-credit')">{{ __('Cancel') }}</button>
                        <button class="btn-primary" @disabled($openSales->isEmpty())>{{ __('Collect') }}</button>
                    </div>
                </form>
            </x-ui.dialog>
        @endcan
    </div>
</x-app-layout>
