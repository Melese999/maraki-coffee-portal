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
            class="fixed inset-0 bg-espresso/70 backdrop-blur-md dark:bg-black/70"
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
            class="dialog-panel relative z-10 flex w-full {{ $maxWidthClass }} max-h-[min(92vh,880px)] flex-col overflow-hidden"
            {{ $attributes }}
        >
            <div class="sticky top-0 z-10 flex items-start justify-between gap-3 border-b border-amber/20 bg-[#1C120C]/95 px-5 py-4 backdrop-blur">
                <div class="min-w-0">
                    <h2 id="dialog-title-{{ $name }}" class="text-lg font-extrabold text-amber">{{ $title }}</h2>
                    @if ($subtitle)
                        <p class="mt-1 text-sm text-parchment/55">{{ $subtitle }}</p>
                    @endif
                </div>
                <button
                    type="button"
                    class="shrink-0 text-2xl leading-none text-amber-soft transition hover:text-amber"
                    x-on:click="close()"
                    aria-label="{{ __('Close') }}"
                >&times;</button>
            </div>

            <div class="dialog-body flex-1 overflow-y-auto px-5 py-4">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="sticky bottom-0 z-10 flex flex-wrap items-center justify-end gap-2 border-t border-amber/20 bg-[#1C120C]/95 px-5 py-4 backdrop-blur">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
