<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
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
<body class="font-sans">
    <div class="tibeb-bar"></div>
    <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-16 top-10 h-56 w-56 rounded-full bg-terracotta/10 blur-3xl"></div>
            <div class="absolute -right-10 bottom-10 h-64 w-64 rounded-full bg-amber/15 blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <x-brand-logo class="justify-center" />
                <p class="mt-4 text-sm text-bean dark:text-parchment/60">{{ __('Ceremony') }} · {{ __('Track sales, expenses, credit, and loans.') }}</p>
            </div>

            <div class="panel p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
