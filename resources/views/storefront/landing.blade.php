<x-storefront-layout :title="__('Home')">
    {{-- Hero: one composition — brand, line, CTAs, full-bleed ceremony --}}
    <section class="relative isolate min-h-[100svh] overflow-hidden bg-espresso">
        <img
            src="{{ asset('images/storefront/hero.jpg') }}"
            alt="{{ __('Ethiopian coffee ceremony') }}"
            class="storefront-kenburns absolute inset-0 h-full w-full object-cover"
        >
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_0%,rgba(28,18,12,0.35)_45%,rgba(28,18,12,0.92)_100%)]"></div>
        <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-espresso via-espresso/80 to-transparent"></div>

        <div class="relative mx-auto flex min-h-[100svh] max-w-6xl flex-col justify-end px-4 pb-24 pt-28 sm:px-6 sm:pb-28">
            <div class="max-w-3xl">
                <h1 class="storefront-rise font-display text-[clamp(2.75rem,9vw,5.75rem)] leading-[0.95] tracking-tight text-parchment">
                    {{ __('Maraki Coffee House') }}
                </h1>
                <p class="storefront-rise storefront-rise-delay-2 mt-5 max-w-lg text-base leading-relaxed text-parchment/80 sm:text-lg">
                    {{ __('Highland beans. Jebena pour. Order pickup or delivery — pay when we meet.') }}
                </p>
                <div class="storefront-rise storefront-rise-delay-3 mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('storefront.order') }}" class="inline-flex items-center rounded-2xl bg-amber px-6 py-3.5 text-sm font-bold text-obsidian shadow-modal transition hover:bg-amber-soft">
                        {{ __('Order from the menu') }}
                    </a>
                    <a href="#origin" class="inline-flex items-center rounded-2xl border border-parchment/25 px-5 py-3.5 text-sm font-semibold text-parchment/90 backdrop-blur-sm transition hover:border-parchment/50 hover:bg-white/5">
                        {{ __('Our origin') }}
                    </a>
                </div>
            </div>

            <a href="#origin" class="storefront-rise storefront-rise-delay-4 absolute bottom-6 left-1/2 flex -translate-x-1/2 flex-col items-center gap-1 text-parchment/45 transition hover:text-parchment/80">
                <span class="text-[10px] font-semibold uppercase tracking-[0.22em]">{{ __('Scroll') }}</span>
                <span class="text-lg leading-none">↓</span>
            </a>
        </div>
    </section>

    {{-- Origin story with full-bleed ceremony image --}}
    <section id="origin" class="relative overflow-hidden bg-espresso text-parchment">
        <div class="grid lg:grid-cols-2">
            <div class="relative min-h-[420px] lg:min-h-full">
                <img
                    src="{{ asset('images/storefront/ceremony.jpg') }}"
                    alt="{{ __('Ethiopian coffee ceremony') }}"
                    class="absolute inset-0 h-full w-full object-cover"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-espresso/40 max-lg:bg-gradient-to-t max-lg:from-espresso max-lg:via-transparent max-lg:to-transparent"></div>
            </div>
            <div class="relative flex flex-col justify-center px-6 py-16 sm:px-10 lg:px-14 lg:py-24">
                <div class="tibeb-bar mb-8 max-w-[12rem]"></div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber">{{ __('Ethiopia · origin of coffee') }}</p>
                <h2 class="mt-4 font-display text-4xl leading-tight sm:text-5xl">
                    {{ __('Coffee was born here.') }}
                </h2>
                <p class="mt-5 max-w-md text-base leading-relaxed text-parchment/70">
                    {{ __('Maraki carries the highland cup with ceremony — roast, pour, share. Browse the menu, tell us pickup or delivery, and settle at the shop.') }}
                </p>
                <a href="{{ route('storefront.order') }}" class="mt-8 inline-flex w-fit items-center rounded-2xl bg-amber px-5 py-3 text-sm font-bold text-obsidian transition hover:bg-amber-soft">
                    {{ __('Start an order') }}
                </a>
            </div>
        </div>
    </section>

    {{-- How it works — one purpose --}}
    <section class="relative border-y border-bean/10 bg-parchment-soft py-16 dark:border-white/10 dark:bg-obsidian-soft sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="habesha-divider mb-10">
                <span class="text-xs font-bold uppercase tracking-[0.2em]">{{ __('How ordering works') }}</span>
            </div>
            <div class="grid gap-8 sm:grid-cols-3">
                @foreach ([
                    ['1', __('Choose'), __('Pick drinks and bites from today’s menu.')],
                    ['2', __('Tell us how'), __('Pickup at the house or delivery to your door.')],
                    ['3', __('Pay at shop'), __('We prepare — you settle when you meet us.')],
                ] as [$num, $title, $copy])
                    <div class="relative pt-2">
                        <div class="font-display text-5xl text-amber/40">{{ $num }}</div>
                        <h3 class="mt-2 font-display text-2xl text-espresso dark:text-parchment">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-bean dark:text-parchment/60">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($featured->isNotEmpty())
        <section class="relative overflow-hidden py-16 sm:py-20">
            <div class="pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-[0.12] max-md:hidden" style="background-image: url('{{ asset('images/storefront/beans.jpg') }}'); background-size: cover; background-position: center;"></div>
            <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
                <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber">{{ __('Today') }}</p>
                        <h2 class="mt-2 font-display text-4xl text-espresso dark:text-parchment">{{ __('From the menu') }}</h2>
                    </div>
                    <a href="{{ route('storefront.order') }}" class="btn-primary">{{ __('See full menu') }}</a>
                </div>

                <div class="divide-y divide-bean/10 border-y border-bean/10 dark:divide-white/10 dark:border-white/10">
                    @foreach ($featured as $item)
                        <a href="{{ route('storefront.order') }}" class="flex items-baseline justify-between gap-4 py-5 transition hover:bg-amber/5">
                            <div class="min-w-0">
                                <div class="font-display text-xl text-espresso dark:text-parchment sm:text-2xl">{{ $item->name }}</div>
                                <div class="mt-1 text-xs uppercase tracking-[0.16em] text-bean/60 dark:text-parchment/45">{{ $item->unit }}</div>
                            </div>
                            <div class="shrink-0 font-display text-xl text-terracotta dark:text-amber sm:text-2xl">
                                {{ \App\Support\Money::format($item->price) }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="relative overflow-hidden bg-espresso py-20 text-center text-parchment">
        <img src="{{ asset('images/storefront/beans.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25" aria-hidden="true">
        <div class="absolute inset-0 bg-espresso/75"></div>
        <div class="relative mx-auto max-w-2xl px-4">
            <div class="tibeb-bar mx-auto mb-8 max-w-[10rem]"></div>
            <h2 class="font-display text-4xl sm:text-5xl">{{ __('Ready for a cup?') }}</h2>
            <p class="mx-auto mt-4 max-w-md text-parchment/70">{{ __('Build your order in a minute. We will take it from there.') }}</p>
            <a href="{{ route('storefront.order') }}" class="mt-8 inline-flex items-center rounded-2xl bg-amber px-8 py-3.5 text-sm font-bold text-obsidian transition hover:bg-amber-soft">
                {{ __('Order now') }}
            </a>
        </div>
    </section>
</x-storefront-layout>
