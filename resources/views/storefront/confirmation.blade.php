<x-storefront-layout :title="__('Order :number', ['number' => $order->order_number])">
    <section class="relative overflow-hidden bg-espresso px-4 pb-16 pt-14 text-parchment sm:px-6 sm:pt-20">
        <img src="{{ asset('images/storefront/ceremony.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-b from-espresso/60 via-espresso/85 to-espresso"></div>
        <div class="relative mx-auto max-w-2xl text-center">
            <div class="tibeb-bar mx-auto mb-6 max-w-[9rem]"></div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber">{{ __('Order received') }}</p>
            <h1 class="mt-3 font-display text-4xl tracking-tight sm:text-5xl">{{ $order->order_number }}</h1>
            <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-parchment/70">
                {{ __('We have your order. Pay at the shop when you collect or when delivery arrives.') }}
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 sm:py-12">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-highland/30 bg-highland/10 px-4 py-3 text-sm text-highland dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl border border-bean/10 bg-white shadow-soft dark:border-white/10 dark:bg-obsidian-card">
            <div class="grid gap-4 border-b border-bean/10 px-5 py-5 sm:grid-cols-3 dark:border-white/10">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-bean/60 dark:text-parchment/45">{{ __('Status') }}</div>
                    <div class="mt-1 font-semibold">{{ $order->status->label() }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-bean/60 dark:text-parchment/45">{{ __('Fulfillment') }}</div>
                    <div class="mt-1 font-semibold">{{ $order->fulfillment_type->label() }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-bean/60 dark:text-parchment/45">{{ __('Payment') }}</div>
                    <div class="mt-1 font-semibold">{{ __('Pay at shop') }}</div>
                </div>
            </div>

            <div class="grid gap-4 border-b border-bean/10 px-5 py-5 sm:grid-cols-2 dark:border-white/10">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-bean/60 dark:text-parchment/45">{{ __('Name') }}</div>
                    <div class="mt-1 font-medium">{{ $order->customer_name }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-bean/60 dark:text-parchment/45">{{ __('Phone') }}</div>
                    <div class="mt-1 font-medium">{{ $order->customer_phone }}</div>
                </div>
                @if ($order->fulfillment_type->value === 'delivery')
                    <div class="sm:col-span-2">
                        <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-bean/60 dark:text-parchment/45">{{ __('Delivery address') }}</div>
                        <div class="mt-1 font-medium">{{ $order->delivery_address }}</div>
                    </div>
                @endif
            </div>

            <div class="space-y-3 px-5 py-5">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span>{{ $item->quantity }}× {{ $item->item_name }}</span>
                        <span class="font-semibold">{{ \App\Support\Money::format($item->line_total) }}</span>
                    </div>
                @endforeach
                <div class="flex items-center justify-between gap-3 border-t border-bean/10 pt-4 dark:border-white/10">
                    <span class="font-semibold">{{ __('Total') }}</span>
                    <span class="font-display text-2xl text-espresso dark:text-parchment">{{ \App\Support\Money::format($order->total) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('storefront.order') }}" class="inline-flex items-center rounded-2xl bg-amber px-6 py-3 text-sm font-bold text-obsidian transition hover:bg-amber-soft">
                {{ __('Order again') }}
            </a>
            <a href="{{ route('storefront.home') }}" class="btn-secondary">{{ __('Back home') }}</a>
        </div>
    </div>
</x-storefront-layout>
