<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@isset($title){{ $title }} · @endisset{{ config('app.name') }}</title>
    @include('partials.pwa')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|fraunces:500,600,700|noto-sans-ethiopic:400,500,600|noto-serif-ethiopic:500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (() => {
            const dark = localStorage.getItem('maraki-theme') === 'dark'
                || (!localStorage.getItem('maraki-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
</head>
<body class="!overflow-y-auto bg-parchment font-sans text-espresso antialiased dark:bg-obsidian dark:text-parchment">
    <div class="tibeb-bar"></div>

    <header class="sticky top-0 z-40 border-b border-white/10 bg-espresso/80 text-parchment backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3.5 sm:px-6">
            <a href="{{ route('storefront.home') }}" class="min-w-0">
                <x-brand-logo class="[&_.font-display]:text-parchment [&_.text-bean]:text-amber/80 [&_.text-espresso]:text-parchment" />
            </a>

            <nav class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('storefront.order') }}" class="hidden text-sm font-medium text-parchment/70 transition hover:text-parchment sm:inline">
                    {{ __('Order') }}
                </a>

                <div class="inline-flex rounded-full border border-white/15 bg-black/20 p-1">
                    <a
                        href="{{ route('locale.switch', 'en') }}"
                        @class([
                            'rounded-full px-2.5 py-1 text-xs font-semibold transition',
                            'bg-amber text-obsidian' => app()->getLocale() === 'en',
                            'text-parchment/70' => app()->getLocale() !== 'en',
                        ])
                    >EN</a>
                    <a
                        href="{{ route('locale.switch', 'am') }}"
                        @class([
                            'rounded-full px-2.5 py-1 text-xs font-semibold transition',
                            'bg-amber text-obsidian' => app()->getLocale() === 'am',
                            'text-parchment/70' => app()->getLocale() !== 'am',
                        ])
                    >አማ</a>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-xl border border-white/15 px-3 py-1.5 text-xs font-semibold text-parchment/80 hover:bg-white/5">{{ __('Portal') }}</a>
                @else
                    <a href="{{ route('login') }}" class="hidden text-xs font-medium text-parchment/55 hover:text-parchment sm:inline">{{ __('Staff') }}</a>
                @endauth

                <a href="{{ route('storefront.order') }}" class="inline-flex items-center rounded-xl bg-amber px-3 py-2 text-xs font-bold text-obsidian shadow-soft transition hover:bg-amber-soft sm:px-4 sm:text-sm">
                    {{ __('Order now') }}
                </a>
            </nav>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="relative mt-0 overflow-hidden bg-espresso text-parchment">
        <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image: url('{{ asset('images/storefront/beans.jpg') }}'); background-size: cover; background-position: center;"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-espresso via-espresso/92 to-espresso/80"></div>
        <div class="relative mx-auto flex max-w-6xl flex-col gap-6 px-4 py-14 sm:flex-row sm:items-end sm:justify-between sm:px-6">
            <div>
                <div class="font-display text-3xl tracking-tight sm:text-4xl">{{ __('Maraki Coffee House') }}</div>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-parchment/65">{{ __('Born where coffee began — served with ceremony.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <a href="{{ route('storefront.order') }}" class="font-semibold text-amber hover:text-amber-soft">{{ __('Order') }}</a>
                <a href="{{ route('login') }}" class="text-parchment/50 hover:text-parchment">{{ __('Staff login') }}</a>
            </div>
        </div>
        <div class="relative tibeb-bar"></div>
    </footer>
</body>
</html>
