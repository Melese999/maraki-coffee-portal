@props(['class' => 'h-10 w-10'])

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 40 40', 'fill' => 'none', 'xmlns' => 'http://www.w3.org/2000/svg']) }} aria-hidden="true">
    <ellipse cx="18" cy="28" rx="12" ry="5" stroke="currentColor" stroke-width="1.8"/>
    <path d="M6 28V18c0-5 5.4-9 12-9s12 4 12 9v10" stroke="currentColor" stroke-width="1.8"/>
    <path d="M30 18c4 .8 6 3 6 5.5S34 28 30 29" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    <path d="M12 8c1.5-2 4-3 6-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".6"/>
</svg>
