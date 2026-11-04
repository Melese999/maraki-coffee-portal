@props([
    'mobile' => false,
])

<aside
    @if ($mobile)
        class="fixed inset-y-0 left-0 z-50 m-0 flex w-64 max-w-[85vw] flex-col border-r border-bean/10 bg-espresso p-0 text-parchment dark:border-white/5 dark:bg-obsidian md:hidden"
        x-show="$store.sidebar.open"
        x-cloak
        x-transition:enter="transition transform ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        x-on:keydown.escape.window="$store.sidebar.close()"
    @else
        class="fixed inset-y-0 left-0 z-40 m-0 hidden w-64 flex-col border-r border-bean/10 bg-espresso p-0 text-parchment dark:border-white/5 dark:bg-obsidian md:flex"
    @endif
>
    <div class="tibeb-bar shrink-0"></div>

    <div class="flex shrink-0 items-start justify-between gap-2 border-b border-white/10 p-5">
        <x-brand-logo class="[&_.font-display]:text-parchment [&_.text-bean]:text-amber/80" />
        @if ($mobile)
            <button
                type="button"
                class="btn-icon mt-0.5 text-parchment/70"
                x-on:click="$store.sidebar.close()"
                aria-label="{{ __('Close') }}"
            >✕</button>
        @endif
    </div>

    <nav
        class="min-h-0 flex-1 space-y-1 overflow-y-auto p-3"
        @if ($mobile)
            x-on:click="$store.sidebar.close()"
        @endif
    >
        @include('layouts.partials.nav-links', ['variant' => 'sidebar'])
    </nav>

    <div class="shrink-0 border-t border-white/10 p-4 text-sm text-parchment/70">
        <div class="mb-3 flex gap-1 rounded-xl bg-white/5 p-1">
            <a
                href="{{ route('locale.switch', 'en') }}"
                @class([
                    'flex-1 rounded-lg px-2 py-1.5 text-center text-xs font-semibold transition',
                    'bg-amber text-obsidian' => app()->getLocale() === 'en',
                    'text-parchment/70 hover:bg-white/10' => app()->getLocale() !== 'en',
                ])
            >EN</a>
            <a
                href="{{ route('locale.switch', 'am') }}"
                @class([
                    'flex-1 rounded-lg px-2 py-1.5 text-center text-xs font-semibold transition',
                    'bg-amber text-obsidian' => app()->getLocale() === 'am',
                    'text-parchment/70 hover:bg-white/10' => app()->getLocale() !== 'am',
                ])
            >አማ</a>
        </div>
        <div class="font-medium text-parchment">{{ auth()->user()->name }}</div>
        <div class="text-xs">{{ auth()->user()->getRoleNames()->join(', ') }}</div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="text-amber hover:underline">{{ __('Log out') }}</button>
        </form>
    </div>
</aside>
