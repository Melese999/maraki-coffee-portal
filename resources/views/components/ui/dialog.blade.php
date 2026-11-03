@props([
    'name',
    'title' => '',
    'subtitle' => null,
    'maxWidth' => 'lg',
    'show' => false,
])

@php
    $maxWidthClass = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
    ][$maxWidth] ?? 'sm:max-w-lg';
@endphp

<div
    x-data="{
        show: @js($show),
        open() {
            this.show = true;
            document.body.classList.add('overflow-hidden');
        },
        close() {
            this.show = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    x-init="
        $watch('show', value => {
            document.body.classList.toggle('overflow-hidden', value);
        });
        if (show) document.body.classList.add('overflow-hidden');
    "
    x-on:open-modal.window="if ($event.detail === '{{ $name }}' || $event.detail?.name === '{{ $name }}') open()"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}' || $event.detail?.name === '{{ $name }}') close()"
    x-on:keydown.escape.window="if (show) close()"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[60] overflow-y-auto"
    style="display: none;"
>
    <div class="flex min-h-full items-end justify-center px-3 py-6 sm:items-center sm:p-6">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-obsidian/50 backdrop-blur-md dark:bg-black/60"
            x-on:click="close()"
            aria-hidden="true"
        ></div>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            role="dialog"
            aria-modal="true"
            aria-labelledby="dialog-title-{{ $name }}"
            class="relative z-10 flex w-full {{ $maxWidthClass }} max-h-[min(92vh,880px)] flex-col overflow-hidden rounded-3xl border border-bean/10 bg-parchment shadow-modal dark:border-white/10 dark:bg-obsidian-card"
            {{ $attributes }}
        >
            <div class="sticky top-0 z-10 flex items-start justify-between gap-3 border-b border-bean/10 bg-parchment/95 px-5 py-4 backdrop-blur dark:border-white/10 dark:bg-obsidian-card/95">
                <div class="min-w-0">
                    <div class="tibeb-bar mb-3 rounded-full opacity-80"></div>
                    <h2 id="dialog-title-{{ $name }}" class="font-display text-xl text-espresso dark:text-parchment">{{ $title }}</h2>
                    @if ($subtitle)
                        <p class="mt-1 text-sm text-bean dark:text-parchment/60">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" class="btn-icon shrink-0" x-on:click="close()" aria-label="{{ __('Close') }}">
                    <span class="text-lg leading-none">&times;</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-4">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="sticky bottom-0 z-10 flex flex-wrap items-center justify-end gap-2 border-t border-bean/10 bg-parchment/95 px-5 py-4 backdrop-blur dark:border-white/10 dark:bg-obsidian-card/95">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
