<x-app-layout>
    <x-slot name="header">{{ __('Daily sales') }}</x-slot>

    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="label" for="from">{{ __('From') }}</label>
                <input class="field" type="date" id="from" name="from" value="{{ $from }}">
            </div>
            <div>
                <label class="label" for="to">{{ __('To') }}</label>
                <input class="field" type="date" id="to" name="to" value="{{ $to }}">
            </div>
            <button class="btn-secondary">{{ __('Filter') }}</button>
        </form>

        @can('sales.create')
            <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-sale')">
                <x-ui.motif-cini class="h-5 w-5" />
                {{ __('Log sale') }}
            </button>
        @endcan
    </div>

    <section class="panel overflow-hidden">
        <div class="hidden overflow-x-auto md:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Items') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $sale->sale_date->translatedFormat('M j, Y') }}</div>
                                <div class="text-xs text-bean dark:text-parchment/50">
                                    {{ $sale->created_at->format('H:i') }} · {{ $sale->recorder->name }}
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($sale->items as $item)
                                        <span class="badge-soft">{{ $item->quantity }}× {{ $item->item_name }}</span>
                                    @endforeach
                                </div>
                                @if ($sale->customer)
                                    <div class="mt-1 text-xs text-bean dark:text-parchment/50">{{ $sale->customer->name }}</div>
                                @endif
                            </td>
                            <td>
                                <span @class([$sale->payment_status->value === 'paid' ? 'badge-paid' : 'badge-credit'])>
                                    {{ $sale->payment_status->label() }}
                                </span>
                            </td>
                            <td class="font-semibold">{{ \App\Support\Money::format($sale->total) }}</td>
                            <td class="text-right">
                                @can('sales.delete')
                                    <x-ui.icon-button
                                        tone="danger"
                                        x-data
                                        x-on:click="$dispatch('open-modal', { name: 'delete-sale-{{ $sale->id }}' })"
                                        aria-label="{{ __('Void sale') }}"
                                    >
                                        ⌫
                                    </x-ui.icon-button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-bean dark:text-parchment/55">{{ __('No sales for this day.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-4 md:hidden">
            @forelse ($sales as $sale)
                <article class="rounded-2xl border border-bean/10 bg-parchment-soft/60 p-4 dark:border-white/5 dark:bg-obsidian-soft">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold">{{ $sale->sale_date->translatedFormat('M j, Y') }}</div>
                            <div class="text-xs text-bean dark:text-parchment/50">{{ $sale->created_at->format('H:i') }}</div>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach ($sale->items as $item)
                                    <span class="badge-soft">{{ $item->quantity }}× {{ $item->item_name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold">{{ \App\Support\Money::format($sale->total) }}</div>
                            <span @class(['mt-1 inline-flex', $sale->payment_status->value === 'paid' ? 'badge-paid' : 'badge-credit'])>
                                {{ $sale->payment_status->label() }}
                            </span>
                        </div>
                    </div>
                    @can('sales.delete')
                        <div class="mt-3 flex justify-end">
                            <button type="button" class="btn-ghost text-terracotta" x-data x-on:click="$dispatch('open-modal', 'delete-sale-{{ $sale->id }}')">{{ __('Void') }}</button>
                        </div>
                    @endcan
                </article>
            @empty
                <p class="py-8 text-center text-sm text-bean dark:text-parchment/55">{{ __('No sales for this day.') }}</p>
            @endforelse
        </div>

        <div class="border-t border-bean/10 p-4 dark:border-white/5">{{ $sales->links() }}</div>
    </section>

    <div class="modals-root">
        @can('sales.create')
            <x-ui.dialog
                name="create-sale"
                :title="__('Log sale')"
                :subtitle="__('Pull items from the active Maraki menu.')"
                maxWidth="2xl"
                :show="$errors->any() && old('_form') === 'sale'"
            >
                @include('sales._form', [
                    'menuItems' => $menuItems,
                    'customers' => $customers,
                    'date' => $date,
                    'filterFrom' => $from,
                    'filterTo' => $to,
                ])
            </x-ui.dialog>
        @endcan

        @can('sales.delete')
            @foreach ($sales as $sale)
                <x-ui.dialog name="delete-sale-{{ $sale->id }}" :title="__('Void sale?')" :subtitle="__('This soft-deletes the sale for audit safety.')" maxWidth="md">
                    <p class="text-sm text-bean dark:text-parchment/70">
                        {{ __('Void :amount recorded at :time?', ['amount' => \App\Support\Money::format($sale->total), 'time' => $sale->created_at->format('H:i')]) }}
                    </p>
                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" class="mt-6 flex justify-end gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-sale-{{ $sale->id }}')">{{ __('Cancel') }}</button>
                        <button class="btn-danger">{{ __('Void sale') }}</button>
                    </form>
                </x-ui.dialog>
            @endforeach
        @endcan
    </div>
</x-app-layout>
