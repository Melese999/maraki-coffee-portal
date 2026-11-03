@props(['class' => 'h-16 w-16'])

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 64 64', 'fill' => 'none', 'xmlns' => 'http://www.w3.org/2000/svg']) }} aria-hidden="true">
    <path d="M32 8c-7 7-12 14-12 22a12 12 0 0 0 24 0c0-8-5-15-12-22Z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
    <path d="M22 42h20c2.5 4 2.5 7 0 9H22c-2.5-2-2.5-5 0-9Z" fill="currentColor" opacity=".88"/>
    <path d="M44 28c5 1 8 4 8 8s-3 7-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    <circle cx="20" cy="56" r="2.2" fill="currentColor"/>
    <circle cx="32" cy="56" r="2.2" fill="currentColor"/>
    <circle cx="44" cy="56" r="2.2" fill="currentColor"/>
</svg>
