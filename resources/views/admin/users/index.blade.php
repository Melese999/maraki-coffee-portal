<x-app-layout>
    <x-slot name="header">{{ __('Users') }}</x-slot>

    <div class="mb-5 flex justify-end">
        @can('users.create')
            <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-user')">{{ __('Create user') }}</button>
        @endcan
    </div>

    <section class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-xs text-bean dark:text-parchment/50">{{ $user->email }}</div>
                            </td>
                            <td><span class="badge-soft">{{ $user->getRoleNames()->join(', ') }}</span></td>
                            <td>
                                <span @class([$user->is_active ? 'badge-paid' : 'badge-credit'])>
                                    {{ $user->is_active ? __('Active') : __('Disabled') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="inline-flex gap-2">
                                    @can('users.update')
                                        <x-ui.icon-button tone="edit" x-data x-on:click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')" aria-label="{{ __('Edit') }}">✎</x-ui.icon-button>
                                    @endcan
                                    @can('users.delete')
                                        @if (auth()->id() !== $user->id)
                                            <x-ui.icon-button tone="danger" x-data x-on:click="$dispatch('open-modal', 'delete-user-{{ $user->id }}')" aria-label="{{ __('Delete') }}">⌫</x-ui.icon-button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-bean/10 p-4 dark:border-white/5">{{ $users->links() }}</div>
    </section>

    <div class="modals-root">
        @can('users.create')
            <x-ui.dialog name="create-user" :title="__('Create user')" maxWidth="xl" :show="$errors->any() && old('_form') === 'user-create'">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="user-create">
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('Identity & contact') }}</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">{{ __('Name') }}</label>
                                <input class="field" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div>
                                <label class="label">{{ __('Email') }}</label>
                                <input class="field" type="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div>
                                <label class="label">{{ __('Password') }}</label>
                                <input class="field" type="password" name="password" required>
                            </div>
                            <div>
                                <label class="label">{{ __('Role') }}</label>
                                <select class="field" name="role" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <label class="mt-4 inline-flex items-center gap-2 text-sm text-parchment/80">
                            <input type="checkbox" name="is_active" value="1" checked> {{ __('Active') }}
                        </label>
                    </section>
                    <div class="dialog-actions">
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'create-user')">{{ __('Cancel') }}</button>
                        <button class="btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </x-ui.dialog>
        @endcan

        @foreach ($users as $user)
            @can('users.update')
                <x-ui.dialog name="edit-user-{{ $user->id }}" :title="__('Edit :name', ['name' => $user->name])" maxWidth="xl">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <section class="dialog-section">
                            <h3 class="dialog-section-title">{{ __('Identity & contact') }}</h3>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="label">{{ __('Name') }}</label>
                                    <input class="field" name="name" value="{{ $user->name }}" required>
                                </div>
                                <div>
                                    <label class="label">{{ __('Email') }}</label>
                                    <input class="field" type="email" name="email" value="{{ $user->email }}" required>
                                </div>
                                <div>
                                    <label class="label">{{ __('New password') }}</label>
                                    <input class="field" type="password" name="password" placeholder="{{ __('Optional') }}">
                                </div>
                                <div>
                                    <label class="label">{{ __('Role') }}</label>
                                    <select class="field" name="role" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label class="mt-4 inline-flex items-center gap-2 text-sm text-parchment/80">
                                <input type="checkbox" name="is_active" value="1" @checked($user->is_active)> {{ __('Active') }}
                            </label>
                        </section>
                        <div class="dialog-actions">
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'edit-user-{{ $user->id }}')">{{ __('Cancel') }}</button>
                            <button class="btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </x-ui.dialog>
            @endcan
            @can('users.delete')
                @if (auth()->id() !== $user->id)
                    <x-ui.dialog name="delete-user-{{ $user->id }}" :title="__('Delete user?')" maxWidth="md">
                        <p class="text-sm text-bean dark:text-parchment/70">{{ __('Delete :name?', ['name' => $user->name]) }}</p>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-6 flex justify-end gap-2">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-user-{{ $user->id }}')">{{ __('Cancel') }}</button>
                            <button class="btn-danger">{{ __('Delete') }}</button>
                        </form>
                    </x-ui.dialog>
                @endif
            @endcan
        @endforeach
    </div>
</x-app-layout>
