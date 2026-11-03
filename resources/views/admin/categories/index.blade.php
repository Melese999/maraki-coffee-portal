<x-app-layout>
    <x-slot name="header">{{ __('Expense categories') }}</x-slot>

    <div class="mb-5 flex justify-end">
        <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-category')">{{ __('Add category') }}</button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($categories as $category)
            <article class="panel p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-display text-lg">{{ $category->name }}</h3>
                        <span @class(['mt-2 inline-flex', $category->is_active ? 'badge-paid' : 'badge-credit'])>
                            {{ $category->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <x-ui.icon-button tone="edit" x-data x-on:click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')" aria-label="{{ __('Edit') }}">✎</x-ui.icon-button>
                        <x-ui.icon-button tone="danger" x-data x-on:click="$dispatch('open-modal', 'delete-category-{{ $category->id }}')" aria-label="{{ __('Delete') }}">⌫</x-ui.icon-button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>

    <div class="modals-root">
        <x-ui.dialog name="create-category" :title="__('Add category')" maxWidth="md" :show="$errors->any() && old('_form') === 'category-create'">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_form" value="category-create">
                <div>
                    <label class="label">{{ __('Name') }}</label>
                    <input class="field" name="name" value="{{ old('name') }}" required>
                </div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" checked> {{ __('Active') }}
                </label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'create-category')">{{ __('Cancel') }}</button>
                    <button class="btn-primary">{{ __('Add') }}</button>
                </div>
            </form>
        </x-ui.dialog>

        @foreach ($categories as $category)
            <x-ui.dialog name="edit-category-{{ $category->id }}" :title="__('Edit category')" maxWidth="md">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="label">{{ __('Name') }}</label>
                        <input class="field" name="name" value="{{ $category->name }}" required>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked($category->is_active)> {{ __('Active') }}
                    </label>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'edit-category-{{ $category->id }}')">{{ __('Cancel') }}</button>
                        <button class="btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </x-ui.dialog>

            <x-ui.dialog name="delete-category-{{ $category->id }}" :title="__('Delete category?')" maxWidth="md">
                <p class="text-sm text-bean dark:text-parchment/70">{{ __('Delete :name?', ['name' => $category->name]) }}</p>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-6 flex justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-category-{{ $category->id }}')">{{ __('Cancel') }}</button>
                    <button class="btn-danger">{{ __('Delete') }}</button>
                </form>
            </x-ui.dialog>
        @endforeach
    </div>
</x-app-layout>
