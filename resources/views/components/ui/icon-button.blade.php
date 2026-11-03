@props([
    'type' => 'button',
    'tone' => 'default',
])

@php
    $toneClass = match ($tone) {
        'danger' => 'hover:border-terracotta/40 hover:text-terracotta dark:hover:text-terracotta-soft',
        'edit' => 'hover:border-amber/40 hover:text-amber-warm dark:hover:text-amber',
        default => '',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "btn-icon {$toneClass}"]) }}>
    {{ $slot }}
</button>
