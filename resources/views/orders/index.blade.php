<x-app-layout>
    <x-slot name="header">
        {{ __('Orders') }}
        @if ($openCount > 0)
            <span class="ms-2 align-middle text-sm font-sans font-semibold text-terracotta dark:text-amber">({{ $openCount }} {{ __('open') }})</span>
        @endif
    </x-slot>

    <div class="mb-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="label" for="status">{{ __('Status') }}</label>
                <select class="field" id="status" name="status">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected($status === $statusOption->value)>{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="fulfillment">{{ __('Fulfillment') }}</label>
                <select class="field" id="fulfillment" name="fulfillment">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($fulfillmentTypes as $type)
                        <option value="{{ $type->value }}" @selected($fulfillment === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-secondary">{{ __('Filter') }}</button>
        </form>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-terracotta/30 bg-terracotta/10 px-4 py-3 text-sm text-terracotta-deep dark:text-terracotta-soft">
            {{ session('error') }}
        </div>
    @endif

    <section class="panel overflow-hidden">
        <div class="hidden overflow-x-auto md:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Order') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Items') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Payment') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $order->order_number }}</div>
                                <div class="text-xs text-bean dark:text-parchment/50">{{ $order->created_at->format('M j, H:i') }}</div>
                                <div class="mt-1 text-xs">{{ $order->fulfillment_type->label() }}</div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $order->customer_name }}</div>
                                <div class="text-xs text-bean dark:text-parchment/50">{{ $order->customer_phone }}</div>
                                @if ($order->delivery_address)
                                    <div class="mt-1 text-xs text-bean dark:text-parchment/50">{{ $order->delivery_address }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($order->items as $item)
                                        <span class="badge-soft">{{ $item->quantity }}× {{ $item->item_name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft">{{ $order->status->label() }}</span>
                            </td>
                            <td>
                                <span @class([$order->payment_status->value === 'paid' ? 'badge-paid' : 'badge-credit'])>
                                    {{ $order->payment_status->label() }}
                                </span>
                                @if ($order->sale_id)
                                    <div class="mt-1 text-xs text-bean dark:text-parchment/50">{{ __('Sale #:id', ['id' => $order->sale_id]) }}</div>
                                @endif
                            </td>
                            <td class="font-semibold">{{ \App\Support\Money::format($order->total) }}</td>
                            <td class="text-right">
                                @can('orders.update')
                                    @include('orders._actions', ['order' => $order])
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-bean dark:text-parchment/55">{{ __('No orders yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-4 md:hidden">
            @forelse ($orders as $order)
                <article class="rounded-2xl border border-bean/10 bg-parchment-soft/60 p-4 dark:border-white/5 dark:bg-obsidian-soft">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold">{{ $order->order_number }}</div>
                            <div class="text-xs text-bean dark:text-parchment/50">{{ $order->created_at->format('M j, H:i') }} · {{ $order->fulfillment_type->label() }}</div>
                            <div class="mt-1 text-sm">{{ $order->customer_name }} · {{ $order->customer_phone }}</div>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach ($order->items as $item)
                                    <span class="badge-soft">{{ $item->quantity }}× {{ $item->item_name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold">{{ \App\Support\Money::format($order->total) }}</div>
                            <div class="mt-1 text-xs">{{ $order->status->label() }}</div>
                            <div class="mt-1 text-xs">{{ $order->payment_status->label() }}</div>
                        </div>
                    </div>
                    @can('orders.update')
                        <div class="mt-3 border-t border-bean/10 pt-3 dark:border-white/10">
                            @include('orders._actions', ['order' => $order])
                        </div>
                    @endcan
                </article>
            @empty
                <p class="py-8 text-center text-sm text-bean dark:text-parchment/55">{{ __('No orders yet.') }}</p>
            @endforelse
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-bean/10 px-4 py-3 dark:border-white/10">
                {{ $orders->links() }}
            </div>
        @endif
    </section>
</x-app-layout>
