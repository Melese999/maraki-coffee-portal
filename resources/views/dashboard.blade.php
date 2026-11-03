@php
    $deltaClass = fn (float $value) => $value > 0
        ? 'text-highland dark:text-emerald-300'
        : ($value < 0 ? 'text-terracotta' : 'text-bean dark:text-parchment/50');
    $deltaPrefix = fn (float $value) => $value > 0 ? '+' : '';
    $presetClass = fn (string $key) => $preset === $key
        ? 'btn-primary py-2'
        : 'btn-secondary py-2';
@endphp

<x-app-layout>
    <x-slot name="header">{{ __('Analytics') }}</x-slot>

    {{-- Date filter --}}
    <div class="mb-5 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-bean dark:text-parchment/60">{{ $dateLabel }} · {{ __(':count days', ['count' => $dayCount]) }}</p>
            <div class="flex flex-wrap items-center gap-2">
                @can('sales.create')
                    <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-sale')">
                        {{ __('Log sale') }}
                    </button>
                @endcan
                @can('expenses.create')
                    <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('open-modal', 'create-expense')">
                        {{ __('Add expense') }}
                    </button>
                @endcan
            </div>
        </div>

        <div class="flex flex-wrap items-end gap-2">
            <a href="{{ route('dashboard', ['preset' => 'today']) }}" class="{{ $presetClass('today') }}">{{ __('Today') }}</a>
            <a href="{{ route('dashboard', ['preset' => '7d']) }}" class="{{ $presetClass('7d') }}">{{ __('Last 7 days') }}</a>
            <a href="{{ route('dashboard', ['preset' => '30d']) }}" class="{{ $presetClass('30d') }}">{{ __('Last 30 days') }}</a>

            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="label" for="from">{{ __('From') }}</label>
                    <input class="field !w-auto py-2" type="date" id="from" name="from" value="{{ $from }}" max="{{ $to }}">
                </div>
                <div>
                    <label class="label" for="to">{{ __('To') }}</label>
                    <input class="field !w-auto py-2" type="date" id="to" name="to" value="{{ $to }}" min="{{ $from }}">
                </div>
                <button class="btn-secondary py-2">{{ __('Apply') }}</button>
            </form>
        </div>
    </div>

    {{-- KPI strip --}}
    <div class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-bean/10 bg-white p-4 dark:border-white/10 dark:bg-obsidian-card">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Cash collected') }}</p>
                <span class="text-xs font-semibold {{ $deltaClass($deltas['cash']) }}">
                    {{ $deltaPrefix($deltas['cash']) }}{{ \App\Support\Money::format($deltas['cash']) }}
                </span>
            </div>
            <p class="mt-2 font-display text-3xl tracking-tight text-espresso dark:text-parchment">
                {{ \App\Support\Money::format($stats['cash_collected']) }}
            </p>
            <p class="mt-1 text-xs text-bean/70 dark:text-parchment/45">
                {{ __('Sales') }} {{ \App\Support\Money::format($stats['cash_sales']) }}
                · {{ __('Credit') }} {{ \App\Support\Money::format($stats['credit_collections']) }}
            </p>
        </div>

        <div class="rounded-2xl border border-bean/10 bg-white p-4 dark:border-white/10 dark:bg-obsidian-card">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Expenses') }}</p>
                <span class="text-xs font-semibold {{ $deltaClass(-$deltas['expenses']) }}">
                    {{ $deltaPrefix($deltas['expenses']) }}{{ \App\Support\Money::format($deltas['expenses']) }}
                </span>
            </div>
            <p class="mt-2 font-display text-3xl tracking-tight text-terracotta">
                {{ \App\Support\Money::format($stats['expenses']) }}
            </p>
            <p class="mt-1 text-xs text-bean/70 dark:text-parchment/45">{{ __('vs previous period') }}</p>
        </div>

        <div class="rounded-2xl border border-bean/10 bg-white p-4 dark:border-white/10 dark:bg-obsidian-card">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Profit') }}</p>
                <span class="text-xs font-semibold {{ $deltaClass($deltas['profit']) }}">
                    {{ $deltaPrefix($deltas['profit']) }}{{ \App\Support\Money::format($deltas['profit']) }}
                </span>
            </div>
            <p class="mt-2 font-display text-3xl tracking-tight {{ $stats['profit'] >= 0 ? 'text-highland dark:text-emerald-300' : 'text-terracotta' }}">
                {{ \App\Support\Money::format($stats['profit']) }}
            </p>
            <p class="mt-1 text-xs text-bean/70 dark:text-parchment/45">
                {{ __('Cash − expenses') }}
            </p>
        </div>

        <div class="rounded-2xl border border-bean/10 bg-white p-4 dark:border-white/10 dark:bg-obsidian-card">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Cups') }}</p>
                <span class="text-xs font-semibold {{ $deltaClass((float) $deltas['cups']) }}">
                    {{ $deltaPrefix((float) $deltas['cups']) }}{{ $deltas['cups'] }}
                </span>
            </div>
            <p class="mt-2 font-display text-3xl tracking-tight text-espresso dark:text-parchment">{{ $cupsToday }}</p>
            <p class="mt-1 text-xs text-bean/70 dark:text-parchment/45">
                {{ $salesCount }} {{ __('Sales') }}
                · {{ __('avg') }} {{ \App\Support\Money::format($averageTicket) }}
            </p>
        </div>
    </div>

    {{-- Charts + rankings --}}
    <div class="mb-5 grid gap-4 xl:grid-cols-5">
        <section class="rounded-2xl border border-bean/10 bg-white p-5 dark:border-white/10 dark:bg-obsidian-card xl:col-span-3">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-espresso dark:text-parchment">{{ __('Cash vs expense') }}</h2>
                    <p class="text-xs text-bean dark:text-parchment/50">{{ __('Daily trend') }}</p>
                </div>
                <div class="flex gap-3 text-[11px] text-bean dark:text-parchment/55">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-highland"></span>{{ __('Cash collected') }}</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-terracotta"></span>{{ __('Expenses') }}</span>
                </div>
            </div>

            <div class="flex h-44 items-end gap-1 overflow-x-auto sm:gap-2">
                @foreach ($trend as $row)
                    @php
                        $cashH = max(4, round(($row['cash_collected'] / $trendMax) * 100));
                        $expH = max(4, round(($row['expenses'] / $trendMax) * 100));
                    @endphp
                    <div class="group flex min-w-[1.75rem] flex-1 flex-col items-center gap-2 sm:min-w-0">
                        <div class="flex h-36 w-full items-end justify-center gap-0.5">
                            <div
                                class="w-[42%] rounded-t-md bg-highland/85 transition group-hover:bg-highland"
                                style="height: {{ $cashH }}%"
                                title="{{ $row['full_label'] }} {{ __('cash') }} {{ \App\Support\Money::format($row['cash_collected']) }}"
                            ></div>
                            <div
                                class="w-[42%] rounded-t-md bg-terracotta/75 transition group-hover:bg-terracotta"
                                style="height: {{ $expH }}%"
                                title="{{ $row['full_label'] }} {{ __('expense') }} {{ \App\Support\Money::format($row['expenses']) }}"
                            ></div>
                        </div>
                        <div class="text-center">
                            <div class="text-[10px] font-medium text-espresso dark:text-parchment/80">{{ $row['label'] }}</div>
                            <div class="text-[10px] {{ $row['profit'] >= 0 ? 'text-highland' : 'text-terracotta' }}">
                                {{ number_format($row['profit'], 0) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-bean/10 bg-white p-5 dark:border-white/10 dark:bg-obsidian-card xl:col-span-2">
            <div class="mb-4">
                <h2 class="text-sm font-semibold text-espresso dark:text-parchment">{{ __('Top sellers') }}</h2>
                <p class="text-xs text-bean dark:text-parchment/50">{{ __('By cups sold') }}</p>
            </div>
            <div class="space-y-3">
                @forelse ($topItems as $item)
                    @php $pct = round(((int) $item->qty / $topItemsMax) * 100); @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-2 text-sm">
                            <span class="truncate font-medium text-espresso dark:text-parchment">{{ $item->item_name }}</span>
                            <span class="shrink-0 text-xs text-bean dark:text-parchment/55">{{ (int) $item->qty }} · {{ \App\Support\Money::format($item->revenue) }}</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-parchment-muted dark:bg-obsidian-soft">
                            <div class="h-full rounded-full bg-amber" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-bean dark:text-parchment/55">{{ __('No sales for this period.') }}</p>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Breakdown + attention lists --}}
    <div class="grid gap-4 xl:grid-cols-3">
        <section class="rounded-2xl border border-bean/10 bg-white p-5 dark:border-white/10 dark:bg-obsidian-card">
            <div class="mb-4">
                <h2 class="text-sm font-semibold text-espresso dark:text-parchment">{{ __('Expense mix') }}</h2>
                <p class="text-xs text-bean dark:text-parchment/50">{{ \App\Support\Money::format($periodTotals['expenses']) }} {{ __('total') }}</p>
            </div>
            <div class="space-y-3">
                @forelse ($expenseBreakdown as $row)
                    @php $pct = round(((float) $row->total / $expenseBreakdownMax) * 100); @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-2 text-sm">
                            <span class="truncate font-medium">{{ $row->category?->name ?? '—' }}</span>
                            <span class="shrink-0 text-xs text-bean dark:text-parchment/55">{{ \App\Support\Money::format($row->total) }}</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-parchment-muted dark:bg-obsidian-soft">
                            <div class="h-full rounded-full bg-terracotta/80" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-bean dark:text-parchment/55">{{ __('No expenses for this period.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-bean/10 bg-white p-5 dark:border-white/10 dark:bg-obsidian-card">
            <div class="mb-4 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-espresso dark:text-parchment">{{ __('Unpaid credit') }}</h2>
                    <p class="text-xs text-terracotta">{{ \App\Support\Money::format($unpaidTotal) }}</p>
                </div>
                @can('credit.view')
                    <a href="{{ route('credit.index') }}" class="text-xs font-semibold text-terracotta hover:underline dark:text-amber">{{ __('Manage credit') }}</a>
                @endcan
            </div>
            <div class="divide-y divide-bean/10 dark:divide-white/5">
                @forelse ($unpaidSales as $sale)
                    <div class="flex items-start justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium">{{ $sale->customer?->name ?? __('Customer') }}</div>
                            <div class="truncate text-xs text-bean dark:text-parchment/50">
                                {{ $sale->items->take(2)->map(fn ($i) => $i->quantity.'× '.$i->item_name)->join(', ') }}
                            </div>
                        </div>
                        <div class="shrink-0 text-sm font-semibold text-terracotta">{{ \App\Support\Money::format($sale->balanceDue()) }}</div>
                    </div>
                @empty
                    <p class="text-sm text-bean dark:text-parchment/55">{{ __('No open credit balances.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-bean/10 bg-white p-5 dark:border-white/10 dark:bg-obsidian-card">
            <div class="mb-4 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-espresso dark:text-parchment">{{ __('Open loans') }}</h2>
                    <p class="text-xs text-bean dark:text-parchment/50">{{ \App\Support\Money::format($openLoanTotal) }} {{ __('outstanding') }}</p>
                </div>
                @can('investments.view')
                    <a href="{{ route('investments.index') }}" class="text-xs font-semibold text-terracotta hover:underline dark:text-amber">{{ __('All investments') }}</a>
                @endcan
            </div>
            <div class="divide-y divide-bean/10 dark:divide-white/5">
                @forelse ($openLoans as $loan)
                    <a href="{{ route('investments.show', $loan) }}" class="flex items-start justify-between gap-3 py-2.5 first:pt-0 last:pb-0 hover:opacity-80">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium">{{ $loan->investor->name }}</div>
                            <div class="text-xs text-bean dark:text-parchment/50">{{ __('Since :date', ['date' => $loan->invested_on->format('M j')]) }}</div>
                        </div>
                        <div class="shrink-0 text-sm font-semibold">{{ \App\Support\Money::format($loan->outstanding()) }}</div>
                    </a>
                @empty
                    <p class="text-sm text-bean dark:text-parchment/55">{{ __('No open loans.') }}</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="modals-root">
        @can('sales.create')
            <x-ui.dialog
                name="create-sale"
                :title="__('Log sale')"
                :subtitle="__('Record todays cups from the live menu.')"
                maxWidth="2xl"
                :show="$errors->any() && old('_form') === 'sale'"
            >
                @include('sales._form', ['menuItems' => $menuItems, 'customers' => $customers, 'date' => $date])
            </x-ui.dialog>
        @endcan

        @can('expenses.create')
            <x-ui.dialog
                name="create-expense"
                :title="__('Add expense')"
                :subtitle="__('Choose a category and amount for the day.')"
                maxWidth="xl"
                :show="$errors->any() && old('_form') === 'expense'"
            >
                @include('expenses._form', ['categories' => $categories, 'date' => $date])
            </x-ui.dialog>
        @endcan
    </div>
</x-app-layout>
