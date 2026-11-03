@props(['compact' => false])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span class="brand-mark" aria-hidden="true">
        <svg viewBox="0 0 40 40" class="h-6 w-6" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6c-4 4-7 8-7 13a7 7 0 0 0 14 0c0-5-3-9-7-13Z" stroke="currentColor" stroke-width="2"/>
            <path d="M13 28h14c1.5 3 1.5 5 0 6H13c-1.5-1-1.5-3 0-6Z" fill="currentColor" opacity=".85"/>
            <circle cx="12" cy="34" r="1.6" fill="currentColor"/>
            <circle cx="20" cy="34" r="1.6" fill="currentColor"/>
            <circle cx="28" cy="34" r="1.6" fill="currentColor"/>
        </svg>
    </span>
    @unless($compact)
        <div>
            <div class="font-display text-lg leading-none text-espresso dark:text-parchment">{{ __('Maraki Coffee House') }}</div>
            <div class="mt-1 text-[11px] uppercase tracking-[0.18em] text-bean dark:text-amber/80">{{ __('Ceremony · daily books') }}</div>
        </div>
    @endunless
</div>
