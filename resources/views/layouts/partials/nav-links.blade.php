@php
    $links = [
        ['route' => 'dashboard', 'label' => __('Home'), 'permission' => 'dashboard.view'],
        ['route' => 'sales.index', 'label' => __('Sales'), 'permission' => 'sales.view'],
        ['route' => 'expenses.index', 'label' => __('Expenses'), 'permission' => 'expenses.view'],
        ['route' => 'menu-items.index', 'label' => __('Menu'), 'permission' => 'menu.view'],
        ['route' => 'credit.index', 'label' => __('Credit'), 'permission' => 'credit.view'],
        ['route' => 'investments.index', 'label' => __('Loans'), 'permission' => 'investments.view'],
        ['route' => 'reports.index', 'label' => __('Reports'), 'permission' => 'reports.view'],
        ['route' => 'admin.users.index', 'label' => __('Users'), 'permission' => 'users.view'],
        ['route' => 'admin.roles.index', 'label' => __('Roles'), 'permission' => 'roles.view'],
        ['route' => 'admin.activity.index', 'label' => __('Activity'), 'permission' => 'activity.view'],
        ['route' => 'admin.categories.index', 'label' => __('Categories'), 'permission' => 'categories.manage'],
    ];

    $mobileKeys = ['dashboard', 'sales.index', 'expenses.index', 'credit.index', 'menu-items.index'];
@endphp

@foreach ($links as $link)
    @can($link['permission'])
        @php
            $active = request()->routeIs($link['route'])
                || request()->routeIs(str_replace('.index', '.*', $link['route']));
            $isMobileAllowed = $variant !== 'mobile' || in_array($link['route'], $mobileKeys, true);
        @endphp

        @if ($isMobileAllowed)
            @if ($variant === 'mobile')
                <a href="{{ route($link['route']) }}" @class(['mobile-nav-link', 'mobile-nav-link-active' => $active])>
                    <span class="text-base leading-none">●</span>
                    <span>{{ $link['label'] }}</span>
                </a>
            @else
                <a
                    href="{{ route($link['route']) }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                        'bg-terracotta text-white dark:bg-amber dark:text-obsidian' => $active,
                        'text-parchment/75 hover:bg-white/10 hover:text-parchment' => ! $active,
                    ])
                >
                    <span>{{ $link['label'] }}</span>
                </a>
            @endif
        @endif
    @endcan
@endforeach
