@php
    $menuPayload = $menuItems->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'price' => (float) $item->price,
        'unit' => $item->unit,
        'formatted' => \App\Support\Money::format($item->price),
    ])->values();

    $hadErrors = $errors->any();
    $initialStep = $hadErrors ? 2 : 1;
@endphp

<x-storefront-layout :title="__('Order')">
    <div
        class="relative min-h-[70vh] pb-28 lg:pb-12"
        x-data="marakiCart(@js($menuPayload), @js(old('fulfillment_type', 'pickup')), {{ (int) $initialStep }})"
        x-cloak
    >
        <div class="relative overflow-hidden bg-espresso px-4 pb-10 pt-10 text-parchment sm:px-6 sm:pt-14">
            <img src="{{ asset('images/storefront/beans.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-20" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-b from-espresso/70 via-espresso/85 to-espresso"></div>
            <div class="relative mx-auto max-w-6xl">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber">{{ __('Checkout') }}</p>
                <h1 class="mt-3 font-display text-4xl tracking-tight sm:text-5xl">{{ __('Order from Maraki') }}</h1>
                <p class="mt-3 max-w-xl text-sm text-parchment/65 sm:text-base">
                    {{ __('Add what you love, tell us pickup or delivery, then place the order. Pay at the shop.') }}
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3 sm:gap-4">
                    <template x-for="(label, index) in stepLabels" :key="label">
                        <div class="flex items-center gap-2">
                            <span
                                class="storefront-step"
                                :class="step > index + 1 ? 'storefront-step-done' : (step === index + 1 ? 'storefront-step-active' : 'storefront-step-idle')"
                                x-text="step > index + 1 ? '✓' : (index + 1)"
                            ></span>
                            <span class="text-xs font-semibold sm:text-sm" :class="step === index + 1 ? 'text-parchment' : 'text-parchment/45'" x-text="label"></span>
                            <span class="mx-1 hidden text-parchment/25 sm:inline" x-show="index < stepLabels.length - 1">—</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-terracotta/30 bg-terracotta/10 px-4 py-3 text-sm text-terracotta-deep dark:text-terracotta-soft">
                    <ul class="list-disc space-y-1 ps-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Step 1: Menu --}}
            <section x-show="step === 1" x-transition.opacity.duration.200ms>
                <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="font-display text-3xl text-espresso dark:text-parchment">{{ __('Menu') }}</h2>
                        <p class="mt-1 text-sm text-bean dark:text-parchment/55">{{ __('Tap to add. Adjust quantities as you go.') }}</p>
                    </div>
                    <div class="rounded-full border border-amber/30 bg-amber/10 px-3 py-1 text-xs font-semibold text-espresso dark:text-parchment">
                        <span x-text="cartCount()"></span> {{ __('in cart') }}
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($menuItems as $item)
                        <article class="storefront-menu-row">
                            <div class="min-w-0 flex-1">
                                <div class="font-display text-xl text-espresso dark:text-parchment">{{ $item->name }}</div>
                                <div class="mt-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-bean/55 dark:text-parchment/40">{{ $item->unit }}</div>
                            </div>
                            <div class="font-display text-xl text-terracotta dark:text-amber">{{ \App\Support\Money::format($item->price) }}</div>
                            <div class="flex items-center gap-2" x-show="qtyOf({{ $item->id }}) === 0">
                                <button type="button" class="rounded-xl bg-amber px-4 py-2.5 text-xs font-bold text-obsidian transition hover:bg-amber-soft" @click="addItem({{ $item->id }})">
                                    {{ __('Add') }}
                                </button>
                            </div>
                            <div class="flex items-center gap-2" x-show="qtyOf({{ $item->id }}) > 0" x-cloak>
                                <button type="button" class="btn-icon !h-9 !w-9" @click="dec({{ $item->id }})">−</button>
                                <span class="w-7 text-center text-sm font-bold" x-text="qtyOf({{ $item->id }})"></span>
                                <button type="button" class="btn-icon !h-9 !w-9" @click="inc({{ $item->id }})">+</button>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-bean/20 px-4 py-12 text-center text-sm text-bean dark:border-white/15 dark:text-parchment/55">
                            {{ __('Menu coming soon.') }}
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Step 2: Details --}}
            <section x-show="step === 2" x-transition.opacity.duration.200ms>
                <div class="grid gap-8 lg:grid-cols-[1fr_0.9fr]">
                    <div>
                        <h2 class="font-display text-3xl text-espresso dark:text-parchment">{{ __('Your details') }}</h2>
                        <p class="mt-1 text-sm text-bean dark:text-parchment/55">{{ __('So we can reach you and prepare the order.') }}</p>

                        <div class="mt-6 space-y-5">
                            <div>
                                <label class="label" for="customer_name">{{ __('Your name') }}</label>
                                <input class="field" id="customer_name" x-model="customerName" maxlength="120" autocomplete="name">
                            </div>
                            <div>
                                <label class="label" for="customer_phone">{{ __('Phone') }}</label>
                                <input class="field" id="customer_phone" x-model="customerPhone" maxlength="40" inputmode="tel" autocomplete="tel">
                            </div>

                            <div>
                                <div class="label">{{ __('How should we get it to you?') }}</div>
                                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                    <label class="storefront-choice" :class="fulfillment === 'pickup' && 'storefront-choice-active'">
                                        <input class="sr-only" type="radio" value="pickup" x-model="fulfillment">
                                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-amber">{{ __('Pickup') }}</span>
                                        <span class="text-sm font-semibold text-espresso dark:text-parchment">{{ __('Collect at Maraki') }}</span>
                                        <span class="text-xs text-bean dark:text-parchment/50">{{ __('We will have it ready for you.') }}</span>
                                    </label>
                                    <label class="storefront-choice" :class="fulfillment === 'delivery' && 'storefront-choice-active'">
                                        <input class="sr-only" type="radio" value="delivery" x-model="fulfillment">
                                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-amber">{{ __('Delivery') }}</span>
                                        <span class="text-sm font-semibold text-espresso dark:text-parchment">{{ __('Bring it to me') }}</span>
                                        <span class="text-xs text-bean dark:text-parchment/50">{{ __('Share a clear address below.') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div x-show="fulfillment === 'delivery'" x-cloak x-transition>
                                <label class="label" for="delivery_address">{{ __('Delivery address') }}</label>
                                <textarea class="field" id="delivery_address" rows="3" maxlength="500" x-model="deliveryAddress" placeholder="{{ __('Neighborhood, landmark, building…') }}"></textarea>
                            </div>

                            <div>
                                <label class="label" for="notes">{{ __('Notes') }}</label>
                                <input class="field" id="notes" maxlength="500" x-model="notes" placeholder="{{ __('Optional note') }}">
                            </div>
                        </div>
                    </div>

                    <aside class="rounded-3xl border border-bean/10 bg-white/90 p-5 shadow-soft dark:border-white/10 dark:bg-obsidian-card sm:p-6 lg:sticky lg:top-24 lg:self-start">
                        <h3 class="font-display text-2xl text-espresso dark:text-parchment">{{ __('In your cart') }}</h3>
                        <div class="mt-4 space-y-3">
                            <template x-for="line in cart" :key="line.id">
                                <div class="flex items-center justify-between gap-3 border-b border-bean/10 pb-3 text-sm dark:border-white/10">
                                    <div class="min-w-0">
                                        <div class="truncate font-medium" x-text="line.name"></div>
                                        <div class="text-xs text-bean dark:text-parchment/50">
                                            <span x-text="line.qty"></span>× <span x-text="line.formatted"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="btn-icon !h-8 !w-8 text-sm" @click="dec(line.id)">−</button>
                                        <button type="button" class="btn-icon !h-8 !w-8 text-sm" @click="inc(line.id)">+</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm text-bean dark:text-parchment/55">{{ __('Total') }}</span>
                            <span class="font-display text-2xl text-espresso dark:text-parchment" x-text="formattedTotal()"></span>
                        </div>
                        <p class="mt-3 text-xs text-bean dark:text-parchment/45">{{ __('Payment: pay at the shop. No online payment yet.') }}</p>
                    </aside>
                </div>
            </section>

            {{-- Step 3: Review + submit --}}
            <section x-show="step === 3" x-transition.opacity.duration.200ms>
                <div class="mx-auto max-w-2xl">
                    <h2 class="font-display text-3xl text-espresso dark:text-parchment">{{ __('Confirm your order') }}</h2>
                    <p class="mt-1 text-sm text-bean dark:text-parchment/55">{{ __('Check everything once — then we start preparing.') }}</p>

                    <div class="mt-8 overflow-hidden rounded-3xl border border-bean/10 bg-white shadow-soft dark:border-white/10 dark:bg-obsidian-card">
                        <div class="border-b border-bean/10 bg-espresso px-5 py-4 text-parchment dark:border-white/10">
                            <div class="text-xs font-bold uppercase tracking-[0.16em] text-amber" x-text="fulfillment === 'delivery' ? @js(__('Delivery')) : @js(__('Pickup'))"></div>
                            <div class="mt-1 font-display text-2xl" x-text="customerName"></div>
                            <div class="mt-1 text-sm text-parchment/65" x-text="customerPhone"></div>
                            <div class="mt-2 text-sm text-parchment/70" x-show="fulfillment === 'delivery'" x-text="deliveryAddress"></div>
                        </div>

                        <div class="space-y-3 px-5 py-5">
                            <template x-for="line in cart" :key="'review-'+line.id">
                                <div class="flex justify-between gap-3 text-sm">
                                    <span><span x-text="line.qty"></span>× <span x-text="line.name"></span></span>
                                    <span class="font-semibold" x-text="formatMoney(line.price * line.qty)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between border-t border-bean/10 pt-3 text-base font-semibold dark:border-white/10">
                                <span>{{ __('Total') }}</span>
                                <span class="font-display text-2xl" x-text="formattedTotal()"></span>
                            </div>
                            <p class="text-xs text-bean dark:text-parchment/50" x-show="notes" x-text="'{{ __('Notes') }}: ' + notes"></p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('storefront.order.store') }}" class="mt-6" @submit="prepareSubmit($event)">
                        @csrf
                        <input type="hidden" name="customer_name" :value="customerName">
                        <input type="hidden" name="customer_phone" :value="customerPhone">
                        <input type="hidden" name="fulfillment_type" :value="fulfillment">
                        <input type="hidden" name="delivery_address" :value="deliveryAddress">
                        <input type="hidden" name="notes" :value="notes">
                        <template x-for="(line, index) in cart" :key="'submit-'+line.id">
                            <div>
                                <input type="hidden" :name="`items[${index}][menu_item_id]`" :value="line.id">
                                <input type="hidden" :name="`items[${index}][quantity]`" :value="line.qty">
                            </div>
                        </template>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-amber px-6 py-4 text-sm font-bold text-obsidian shadow-modal transition hover:bg-amber-soft disabled:opacity-50" :disabled="!canSubmit()">
                            {{ __('Place order') }}
                        </button>
                    </form>
                </div>
            </section>
        </div>

        {{-- Sticky action bar --}}
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-bean/10 bg-parchment/95 px-4 py-3 backdrop-blur-xl dark:border-white/10 dark:bg-obsidian/95 lg:static lg:border-0 lg:bg-transparent lg:px-0 lg:py-0 lg:backdrop-blur-none">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 lg:px-6 lg:pb-10">
                <div class="min-w-0">
                    <div class="text-xs text-bean dark:text-parchment/50" x-show="cart.length">
                        <span x-text="cartCount()"></span> {{ __('items') }} · <span class="font-display text-lg text-espresso dark:text-parchment" x-text="formattedTotal()"></span>
                    </div>
                    <div class="text-xs text-bean dark:text-parchment/50" x-show="!cart.length">{{ __('Your cart is empty.') }}</div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="btn-secondary" x-show="step > 1" @click="step = step - 1">{{ __('Back') }}</button>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-xl bg-amber px-5 py-2.5 text-sm font-bold text-obsidian transition hover:bg-amber-soft disabled:opacity-50"
                        x-show="step < 3"
                        :disabled="step === 1 ? !cart.length : !detailsReady()"
                        @click="goNext()"
                    >
                        <span x-text="step === 1 ? @js(__('Continue to details')) : @js(__('Review order'))"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('marakiCart', (menuItems, initialFulfillment = 'pickup', initialStep = 1) => ({
                menuItems,
                cart: [],
                step: initialStep,
                fulfillment: initialFulfillment || 'pickup',
                customerName: @js(old('customer_name', '')),
                customerPhone: @js(old('customer_phone', '')),
                deliveryAddress: @js(old('delivery_address', '')),
                notes: @js(old('notes', '')),
                storageKey: 'maraki-cart-v1',
                stepLabels: [@js(__('Menu')), @js(__('Details')), @js(__('Confirm'))],
                init() {
                    try {
                        const saved = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                        if (Array.isArray(saved)) {
                            this.cart = saved
                                .map((row) => {
                                    const item = this.menuItems.find((m) => m.id === row.id);
                                    if (! item) return null;
                                    return { ...item, qty: Math.max(1, Math.min(50, Number(row.qty) || 1)) };
                                })
                                .filter(Boolean);
                        }
                    } catch (e) {}

                    this.$watch('cart', (value) => {
                        localStorage.setItem(this.storageKey, JSON.stringify(value.map((line) => ({ id: line.id, qty: line.qty }))));
                    }, { deep: true });
                },
                qtyOf(id) {
                    return this.cart.find((row) => row.id === id)?.qty || 0;
                },
                cartCount() {
                    return this.cart.reduce((sum, line) => sum + line.qty, 0);
                },
                addItem(id) {
                    const item = this.menuItems.find((m) => m.id === id);
                    if (! item) return;
                    const existing = this.cart.find((line) => line.id === id);
                    if (existing) {
                        existing.qty = Math.min(50, existing.qty + 1);
                    } else {
                        this.cart.push({ ...item, qty: 1 });
                    }
                },
                inc(id) {
                    const line = this.cart.find((row) => row.id === id);
                    if (line) line.qty = Math.min(50, line.qty + 1);
                },
                dec(id) {
                    const line = this.cart.find((row) => row.id === id);
                    if (! line) return;
                    line.qty -= 1;
                    if (line.qty <= 0) this.cart = this.cart.filter((row) => row.id !== id);
                },
                total() {
                    return this.cart.reduce((sum, line) => sum + (line.price * line.qty), 0);
                },
                formatMoney(value) {
                    return new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
                },
                formattedTotal() {
                    return this.formatMoney(this.total());
                },
                detailsReady() {
                    const base = this.cart.length && this.customerName.trim() && this.customerPhone.trim();
                    if (! base) return false;
                    if (this.fulfillment === 'delivery') return this.deliveryAddress.trim().length > 0;
                    return true;
                },
                canSubmit() {
                    return this.detailsReady();
                },
                goNext() {
                    if (this.step === 1 && this.cart.length) this.step = 2;
                    else if (this.step === 2 && this.detailsReady()) this.step = 3;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                prepareSubmit(event) {
                    if (! this.canSubmit()) {
                        event.preventDefault();
                        this.step = 2;
                        return;
                    }
                    localStorage.removeItem(this.storageKey);
                },
            }));
        });
    </script>
</x-storefront-layout>
