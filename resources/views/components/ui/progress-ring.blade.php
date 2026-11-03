@props([
    'value' => 0,
    'max' => 100,
    'size' => 148,
    'stroke' => 10,
    'label' => null,
    'sublabel' => null,
])

@php
    $pct = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;
    $radius = ($size / 2) - ($stroke * 2);
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference - ($pct / 100) * $circumference;
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-flex items-center justify-center']) }}>
    <svg width="{{ $size }}" height="{{ $size }}" class="progress-ring text-terracotta dark:text-amber">
        <circle
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $radius }}"
            fill="none"
            stroke="currentColor"
            stroke-width="{{ $stroke }}"
            class="opacity-15"
        />
        <circle
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $radius }}"
            fill="none"
            stroke="currentColor"
            stroke-width="{{ $stroke }}"
            stroke-linecap="round"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $offset }}"
            class="transition-all duration-700 ease-out"
        />
    </svg>
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
        <div class="font-display text-3xl text-espresso dark:text-parchment">{{ $value }}</div>
        @if ($label)
            <div class="mt-0.5 text-xs font-medium uppercase tracking-wide text-bean dark:text-parchment/60">{{ $label }}</div>
        @endif
        @if ($sublabel)
            <div class="text-[11px] text-bean/70 dark:text-parchment/45">{{ $sublabel }}</div>
        @endif
    </div>
</div>
