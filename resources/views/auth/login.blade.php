<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4 flex justify-center gap-1 rounded-xl border border-bean/10 bg-parchment-soft p-1 dark:border-white/10 dark:bg-obsidian-soft">
        <a
            href="{{ route('locale.switch', 'en') }}"
            @class([
                'flex-1 rounded-lg px-3 py-1.5 text-center text-xs font-semibold transition',
                'bg-terracotta text-white dark:bg-amber dark:text-obsidian' => app()->getLocale() === 'en',
                'text-bean dark:text-parchment/70' => app()->getLocale() !== 'en',
            ])
        >{{ __('English') }}</a>
        <a
            href="{{ route('locale.switch', 'am') }}"
            @class([
                'flex-1 rounded-lg px-3 py-1.5 text-center text-xs font-semibold transition',
                'bg-terracotta text-white dark:bg-amber dark:text-obsidian' => app()->getLocale() === 'am',
                'text-bean dark:text-parchment/70' => app()->getLocale() !== 'am',
            ])
        >{{ __('Amharic') }}</a>
    </div>

    <div class="mb-6 flex justify-center text-terracotta dark:text-amber">
        <x-ui.motif-jebena class="h-14 w-14" />
    </div>

    <h1 class="mb-1 text-center font-display text-2xl text-espresso dark:text-parchment">{{ __('Welcome back') }}</h1>
    <p class="mb-6 text-center text-sm text-bean dark:text-parchment/60">{{ __('Sign in to Maraki Coffee House') }}</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', 'admin@maraki.local')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-bean/30 text-terracotta shadow-sm focus:ring-terracotta dark:border-white/20 dark:bg-obsidian-soft" name="remember">
                <span class="ms-2 text-sm text-bean dark:text-parchment/60">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-terracotta hover:underline dark:text-amber" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Log in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
