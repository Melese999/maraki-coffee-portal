@php
    $defaultSelected = $printItems->where('is_active', true)->pluck('id')->map(fn ($id) => (string) $id)->values()->all();
    $allIds = $printItems->pluck('id')->map(fn ($id) => (string) $id)->values()->all();
@endphp

<x-app-layout>
    <x-slot name="header">{{ __('Menu items') }}</x-slot>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-bean dark:text-parchment/60">{{ __('Active items available to sell at Maraki.') }}</p>
        <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('open-modal', 'print-menu')">
                    {{ __('PDF menu') }}
                </button>
            @can('menu.create')
                <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-menu')">
                    {{ __('Add menu item') }}
                </button>
            @endcan
        </div>
    </div>

    <x-ui.dialog
        name="print-menu"
        :title="__('PDF menu')"
        :subtitle="__('Choose all items or a custom selection for the PDF menu.')"
        maxWidth="xl"
    >
        <div
            class="space-y-4"
            x-data="{
                selected: @js($defaultSelected),
                allIds: @js($allIds),
                activeIds: @js($defaultSelected),
                get allSelected() {
                    return this.allIds.length > 0 && this.selected.length === this.allIds.length;
                },
                toggleAll() {
                    this.selected = this.allSelected ? [] : [...this.allIds];
                },
                selectActive() {
                    this.selected = [...this.activeIds];
                },
                printSelected() {
                    if (this.selected.length === 0) {
                        return;
                    }
                    const params = new URLSearchParams();
                    this.selected.forEach((id) => params.append('ids[]', id));
                    window.location.href = @js(route('menu-items.print')) + '?' + params.toString();
                }
            }"
        >
            <section class="dialog-section">
                <h3 class="dialog-section-title">{{ __('Select items') }}</h3>
                <div class="mb-3 flex flex-wrap gap-2">
                    <button type="button" class="btn-secondary py-2 text-xs" x-on:click="toggleAll()">
                        <span x-show="!allSelected">{{ __('Select all') }}</span>
                        <span x-show="allSelected" x-cloak>{{ __('Clear all') }}</span>
                    </button>
                    <button type="button" class="btn-secondary py-2 text-xs" x-on:click="selectActive()">
                        {{ __('Active only') }}
                    </button>
                    <span class="self-center text-xs text-parchment/55" x-text="selected.length + ' {{ __('selected') }}'"></span>
                </div>

                <div class="max-h-72 space-y-1 overflow-y-auto rounded-xl border border-amber/20 bg-[#16100E] p-2">
                    @forelse ($printItems as $item)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/5">
                            <input
                                type="checkbox"
                                class="rounded border-amber/30 text-amber focus:ring-amber"
                                value="{{ $item->id }}"
                                x-model="selected"
                            >
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-parchment">{{ $item->name }}</span>
                                <span class="text-xs text-parchment/50">{{ $item->unit }} · {{ $item->is_active ? __('Active') : __('Hidden') }}</span>
                            </span>
                            <span class="shrink-0 text-sm font-semibold text-amber">{{ \App\Support\Money::format($item->price) }}</span>
                        </label>
                    @empty
                        <p class="p-3 text-sm">{{ __('No menu items selected.') }}</p>
                    @endforelse
                </div>
            </section>

            <div class="dialog-actions">
                <button type="button" class="btn-secondary" x-on:click="$dispatch('close-modal', 'print-menu')">{{ __('Cancel') }}</button>
                <button
                    type="button"
                    class="btn-primary"
                    x-bind:disabled="selected.length === 0"
                    x-on:click="printSelected(); $dispatch('close-modal', 'print-menu')"
                    >{{ __('Download PDF') }}</button>
            </div>
        </div>
    </x-ui.dialog>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($items as $item)
            <article class="panel group relative overflow-hidden p-5 transition hover:border-amber/30">
                <div class="absolute -right-3 -top-3 text-amber/10 transition group-hover:text-amber/20">
                    <x-ui.motif-cini class="h-16 w-16" />
                </div>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-display text-xl">{{ $item->name }}</h3>
                        <div class="mt-1 text-sm text-bean dark:text-parchment/60">{{ $item->unit }}</div>
                    </div>
                    <span @class([$item->is_active ? 'badge-paid' : 'badge-credit'])>
                        {{ $item->is_active ? __('Active') : __('Hidden') }}
                    </span>
                </div>
                <div class="mt-4 font-display text-2xl text-terracotta dark:text-amber">{{ \App\Support\Money::format($item->price) }}</div>
                <div class="mt-4 flex gap-2">
                    @can('menu.update')
                        <x-ui.icon-button
                            tone="edit"
                            x-data
                            x-on:click="$dispatch('open-modal', 'edit-menu-{{ $item->id }}')"
                            aria-label="{{ __('Edit item') }}"
                        >✎</x-ui.icon-button>
                    @endcan
                    @can('menu.delete')
                        <x-ui.icon-button
                            tone="danger"
                            x-data
                            x-on:click="$dispatch('open-modal', 'delete-menu-{{ $item->id }}')"
                            aria-label="{{ __('Delete item') }}"
                        >⌫</x-ui.icon-button>
                    @endcan
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    <div class="modals-root">
        @can('menu.create')
            <x-ui.dialog
                name="create-menu"
                :title="__('Add menu item')"
                :subtitle="__('New drink or food available to sell.')"
                maxWidth="lg"
                :show="$errors->any() && old('_form') === 'menu-create'"
            >
                <form method="POST" action="{{ route('menu-items.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="menu-create">
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('Item details') }}</h3>
                        <div>
                            <label class="label" for="name">{{ __('Name') }}</label>
                            <input class="field" name="name" id="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mt-4 grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="label" for="price">{{ __('Price') }}</label>
                                <input class="field" type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price') }}" required>
                            </div>
                            <div>
                                <label class="label" for="unit">{{ __('Unit') }}</label>
                                <input class="field" name="unit" id="unit" value="{{ old('unit', 'cup') }}" required>
                            </div>
                            <div>
                                <label class="label" for="sort_order">{{ __('Sort') }}</label>
                                <input class="field" type="number" min="0" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}">
                            </div>
                        </div>
                        <label class="mt-4 inline-flex items-center gap-2 text-sm text-parchment/80">
                            <input type="checkbox" name="is_active" value="1" checked>
                            {{ __('Available to sell') }}
                        </label>
                    </section>
                    <div class="dialog-actions">
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'create-menu')">{{ __('Cancel') }}</button>
                        <button class="btn-primary">{{ __('Add item') }}</button>
                    </div>
                </form>
            </x-ui.dialog>
        @endcan

        @foreach ($items as $item)
            @can('menu.update')
                <x-ui.dialog
                    name="edit-menu-{{ $item->id }}"
                    :title="__('Edit :name', ['name' => $item->name])"
                    maxWidth="lg"
                    :show="$errors->any() && old('_form') === 'menu-edit-'.$item->id"
                >
                    <form method="POST" action="{{ route('menu-items.update', $item) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_form" value="menu-edit-{{ $item->id }}">
                        <section class="dialog-section">
                            <h3 class="dialog-section-title">{{ __('Item details') }}</h3>
                            <div>
                                <label class="label">{{ __('Name') }}</label>
                                <input class="field" name="name" value="{{ old('name', $item->name) }}" required>
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="label">{{ __('Price') }}</label>
                                    <input class="field" type="number" step="0.01" name="price" value="{{ old('price', $item->price) }}" required>
                                </div>
                                <div>
                                    <label class="label">{{ __('Unit') }}</label>
                                    <input class="field" name="unit" value="{{ old('unit', $item->unit) }}" required>
                                </div>
                                <div>
                                    <label class="label">{{ __('Sort') }}</label>
                                    <input class="field" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}">
                                </div>
                            </div>
                            <label class="mt-4 inline-flex items-center gap-2 text-sm text-parchment/80">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))>
                                {{ __('Active') }}
                            </label>
                        </section>
                        <div class="dialog-actions">
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'edit-menu-{{ $item->id }}')">{{ __('Cancel') }}</button>
                            <button class="btn-primary">{{ __('Save changes') }}</button>
                        </div>
                    </form>
                </x-ui.dialog>
            @endcan

            @can('menu.delete')
                <x-ui.dialog name="delete-menu-{{ $item->id }}" :title="__('Remove menu item?')" maxWidth="md">
                    <p class="text-sm text-bean dark:text-parchment/70">{{ __('Remove :name from the sellable menu?', ['name' => $item->name]) }}</p>
                    <form method="POST" action="{{ route('menu-items.destroy', $item) }}" class="mt-6 flex justify-end gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-menu-{{ $item->id }}')">{{ __('Cancel') }}</button>
                        <button class="btn-danger">{{ __('Delete') }}</button>
                    </form>
                </x-ui.dialog>
            @endcan
        @endforeach
    </div>
</x-app-layout>
