<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data x-init="$store.theme.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @include('partials.pwa')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|fraunces:500,600,700|noto-sans-ethiopic:400,500,600,700|noto-serif-ethiopic:500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    <script>
        (() => {
            const dark = localStorage.getItem('maraki-theme') === 'dark'
                || (!localStorage.getItem('maraki-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
</head>
<body class="m-0 h-full overflow-hidden p-0 font-sans">
    {{-- Desktop sidebar --}}
    @include('layouts.partials.sidebar', ['mobile' => false])

    {{-- Mobile drawer backdrop --}}
    <div
        class="fixed inset-0 z-40 bg-espresso/50 backdrop-blur-sm md:hidden"
        x-show="$store.sidebar.open"
        x-cloak
        x-transition.opacity
        x-on:click="$store.sidebar.close()"
    ></div>

    {{-- Mobile drawer: hidden until logo tap --}}
    @include('layouts.partials.sidebar', ['mobile' => true])

    {{-- Main column sits flush against the sidebar (no body gap) --}}
    <div class="m-0 flex h-full min-h-0 flex-col overflow-y-auto bg-parchment p-0 pb-24 dark:bg-obsidian md:ml-64 md:pb-0">
        <div class="tibeb-bar shrink-0 md:hidden"></div>

        <header class="sticky top-0 z-20 shrink-0 border-b border-bean/10 bg-parchment/95 backdrop-blur dark:border-white/5 dark:bg-obsidian/95">
            <div class="flex w-full items-center justify-between gap-3 px-3 py-3 sm:px-4 lg:px-5">
                <button
                    type="button"
                    class="rounded-xl text-left transition hover:opacity-90 md:hidden"
                    x-on:click="$store.sidebar.toggle()"
                    aria-label="{{ __('Open menu') }}"
                >
                    <x-brand-logo />
                </button>
                <div class="hidden md:block">
                    @isset($header)
                        <h1 class="font-display text-2xl text-espresso dark:text-parchment">{!! $header !!}</h1>
                    @endisset
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex rounded-xl border border-bean/10 bg-white p-0.5 text-xs font-semibold dark:border-white/10 dark:bg-obsidian-card">
                        <a
                            href="{{ route('locale.switch', 'en') }}"
                            @class([
                                'rounded-lg px-2.5 py-1.5 transition',
                                'bg-terracotta text-white dark:bg-amber dark:text-obsidian' => app()->getLocale() === 'en',
                                'text-bean dark:text-parchment/70' => app()->getLocale() !== 'en',
                            ])
                        >EN</a>
                        <a
                            href="{{ route('locale.switch', 'am') }}"
                            @class([
                                'rounded-lg px-2.5 py-1.5 transition',
                                'bg-terracotta text-white dark:bg-amber dark:text-obsidian' => app()->getLocale() === 'am',
                                'text-bean dark:text-parchment/70' => app()->getLocale() !== 'am',
                            ])
                        >አማ</a>
                    </div>
                    <button type="button" class="btn-icon" x-on:click="$store.theme.toggle()" aria-label="{{ __('Toggle theme') }}">
                        <span x-show="! $store.theme.dark">☾</span>
                        <span x-show="$store.theme.dark" x-cloak>☀</span>
                    </button>
                    <div
                        x-data="{
                            canInstall: Boolean(window.marakiInstallPrompt),
                            init() {
                                window.addEventListener('maraki-install-available', () => { this.canInstall = true });
                            },
                            async install() {
                                const prompt = window.marakiInstallPrompt;
                                if (! prompt) {
                                    return;
                                }
                                prompt.prompt();
                                await prompt.userChoice;
                                window.marakiInstallPrompt = null;
                                this.canInstall = false;
                            }
                        }"
                        class="contents"
                    >
                        <button
                            type="button"
                            class="btn-secondary hidden py-1.5 text-xs sm:inline-flex"
                            x-show="canInstall"
                            x-cloak
                            x-on:click="install"
                        >{{ __('Install app') }}</button>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="rounded-full border border-bean/10 bg-white px-3 py-1.5 text-sm font-medium text-bean shadow-soft dark:border-white/10 dark:bg-obsidian-card dark:text-parchment/80">
                        {{ __('Profile') }}
                    </a>
                </div>
            </div>
            <div class="tibeb-bar opacity-70 md:hidden"></div>
        </header>

        <main class="w-full flex-1 px-3 py-4 sm:px-4 lg:px-5">
            @isset($header)
                <h1 class="mb-4 font-display text-2xl text-espresso md:hidden dark:text-parchment">{!! $header !!}</h1>
            @endisset

            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-highland/20 bg-highland/10 px-4 py-3 text-sm text-highland dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-terracotta/20 bg-terracotta/10 px-4 py-3 text-sm text-terracotta-deep dark:text-terracotta-soft">
                    <ul class="list-disc space-y-1 pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-bean/10 bg-parchment/95 px-2 py-1 backdrop-blur dark:border-white/5 dark:bg-obsidian/95 md:hidden">
        <div class="mx-auto flex max-w-lg">
            @include('layouts.partials.nav-links', ['variant' => 'mobile'])
        </div>
    </nav>
</body>
</html>
