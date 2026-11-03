<x-app-layout>
    <x-slot name="header">{{ __('Profit report') }}</x-slot>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label class="label" for="from">{{ __('From') }}</label>
            <input class="field" type="date" name="from" id="from" value="{{ $from }}">
        </div>
        <div>
            <label class="label" for="to">{{ __('To') }}</label>
            <input class="field" type="date" name="to" id="to" value="{{ $to }}">
        </div>
        <button class="btn-primary">{{ __('Apply') }}</button>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Cash collected') }}</div>
            <div class="mt-2 font-display text-2xl">{{ \App\Support\Money::format($totals['cash_collected']) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Expenses') }}</div>
            <div class="mt-2 font-display text-2xl text-terracotta">{{ \App\Support\Money::format($totals['expenses']) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Profit') }}</div>
            <div class="mt-2 font-display text-2xl {{ $totals['profit'] >= 0 ? 'text-highland dark:text-emerald-300' : 'text-terracotta' }}">
                {{ \App\Support\Money::format($totals['profit']) }}
            </div>
        </div>
    </div>

    @php
        $max = max(1, $rows->max(fn ($row) => max($row['cash_collected'], $row['expenses'], abs($row['profit']))));
    @endphp

    <section class="panel p-5">
        <div class="habesha-divider mb-4 text-xs uppercase tracking-[0.18em]">{{ __('Daily trend') }}</div>
        <div class="space-y-3">
            @foreach ($rows as $row)
                <div>
                    <div class="mb-1 flex justify-between text-xs text-bean dark:text-parchment/55">
                        <span>{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j') }}</span>
                        <span>{{ __('Profit') }} {{ \App\Support\Money::format($row['profit']) }}</span>
                    </div>
                    <div class="flex h-3 overflow-hidden rounded-full bg-parchment-muted dark:bg-obsidian-soft">
                        <div class="bg-highland" style="width: {{ round(($row['cash_collected'] / $max) * 100) }}%"></div>
                        <div class="bg-terracotta/80" style="width: {{ round(($row['expenses'] / $max) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex gap-4 text-xs text-bean dark:text-parchment/55">
            <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-highland"></span> {{ __('Cash collected') }}</span>
            <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-terracotta"></span> {{ __('Expenses') }}</span>
        </div>
    </section>
</x-app-layout>
