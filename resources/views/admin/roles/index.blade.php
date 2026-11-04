<x-app-layout>
    <x-slot name="header">{{ __('Roles & permissions') }}</x-slot>

    <div class="mb-5 flex justify-end">
        @can('roles.create')
            <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-role')">{{ __('Create role') }}</button>
        @endcan
    </div>

    <div class="space-y-4">
        @foreach ($roles as $role)
            <section class="panel p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl">{{ $role->name }}</h2>
                        <div class="mt-1 text-xs text-bean dark:text-parchment/55">{{ $role->permissions->count() }} {{ __('permissions') }}</div>
                    </div>
                    <div class="flex gap-2">
                        @can('roles.update')
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('open-modal', 'edit-role-{{ $role->id }}')">{{ __('Edit matrix') }}</button>
                        @endcan
                        @can('roles.delete')
                            @if ($role->name !== 'Admin')
                                <button type="button" class="btn-ghost text-terracotta" x-data x-on:click="$dispatch('open-modal', 'delete-role-{{ $role->id }}')">{{ __('Delete') }}</button>
                            @endif
                        @endcan
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach ($role->permissions->take(12) as $permission)
                        <span class="badge-soft">{{ $permission->name }}</span>
                    @endforeach
                    @if ($role->permissions->count() > 12)
                        <span class="badge-soft">+{{ $role->permissions->count() - 12 }} {{ __('more') }}</span>
                    @endif
                </div>
            </section>
        @endforeach
    </div>

    <div class="modals-root">
        @can('roles.create')
            <x-ui.dialog name="create-role" :title="__('Create role')" maxWidth="3xl" :show="$errors->any() && old('_form') === 'role-create'">
                <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="role-create">
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('Role name') }}</h3>
                        <input class="field max-w-sm" name="name" value="{{ old('name') }}" required>
                    </section>
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('permissions') }}</h3>
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($permissions as $group => $items)
                                <div class="rounded-xl border border-amber/20 bg-[#16100E] p-3">
                                    <div class="mb-2 text-sm font-semibold capitalize text-amber">{{ $group }}</div>
                                    <div class="space-y-1">
                                        @foreach ($items as $permission)
                                            <label class="flex items-center gap-2 text-sm text-parchment/80">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                                {{ $permission->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    <div class="dialog-actions">
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'create-role')">{{ __('Cancel') }}</button>
                        <button class="btn-primary">{{ __('Create role') }}</button>
                    </div>
                </form>
            </x-ui.dialog>
        @endcan

        @foreach ($roles as $role)
            @can('roles.update')
                <x-ui.dialog name="edit-role-{{ $role->id }}" :title="__('Edit :name', ['name' => $role->name])" maxWidth="3xl">
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <section class="dialog-section">
                            <h3 class="dialog-section-title">{{ __('Role name') }}</h3>
                            <input class="field max-w-sm" name="name" value="{{ $role->name }}" @disabled($role->name === 'Admin') required>
                            @if ($role->name === 'Admin')
                                <input type="hidden" name="name" value="Admin">
                            @endif
                        </section>
                        <section class="dialog-section">
                            <h3 class="dialog-section-title">{{ __('permissions') }}</h3>
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                @foreach ($permissions as $group => $items)
                                    <div class="rounded-xl border border-amber/20 bg-[#16100E] p-3">
                                        <div class="mb-2 text-sm font-semibold capitalize text-amber">{{ $group }}</div>
                                        <div class="space-y-1">
                                            @foreach ($items as $permission)
                                                <label class="flex items-center gap-2 text-sm text-parchment/80">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked($role->hasPermissionTo($permission->name))>
                                                    {{ $permission->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                        <div class="dialog-actions">
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'edit-role-{{ $role->id }}')">{{ __('Cancel') }}</button>
                            <button class="btn-primary">{{ __('Save permissions') }}</button>
                        </div>
                    </form>
                </x-ui.dialog>
            @endcan

            @can('roles.delete')
                @if ($role->name !== 'Admin')
                    <x-ui.dialog name="delete-role-{{ $role->id }}" :title="__('Delete role?')" maxWidth="md">
                        <p class="text-sm text-bean dark:text-parchment/70">{{ __('Delete :name?', ['name' => $role->name]) }}</p>
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="mt-6 flex justify-end gap-2">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-role-{{ $role->id }}')">{{ __('Cancel') }}</button>
                            <button class="btn-danger">{{ __('Delete') }}</button>
                        </form>
                    </x-ui.dialog>
                @endif
            @endcan
        @endforeach
    </div>
</x-app-layout>
