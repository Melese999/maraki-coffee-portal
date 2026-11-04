<x-app-layout>
    <x-slot name="header">{{ __('Customers') }}</x-slot>

    <div class="mb-5 flex justify-end">
        @can('customers.create')
            <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-customer')">
                {{ __('Add customer') }}
            </button>
        @endcan
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ($customers as $customer)
            <article class="panel p-5 transition hover:border-amber/30">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-display text-xl">{{ $customer->name }}</h3>
                        <div class="mt-1 text-sm text-bean dark:text-parchment/60">{{ $customer->phone ?: __('No phone') }}</div>
                        @if ($customer->notes)
                            <p class="mt-2 text-sm text-espresso/70 dark:text-parchment/60">{{ $customer->notes }}</p>
                        @endif
                    </div>
                    <span class="badge-credit">{{ \App\Support\Money::format($customer->outstandingBalance()) }}</span>
                </div>
                <div class="mt-4 flex gap-2">
                    @can('customers.update')
                        <x-ui.icon-button tone="edit" x-data x-on:click="$dispatch('open-modal', 'edit-customer-{{ $customer->id }}')" aria-label="{{ __('Edit') }}">✎</x-ui.icon-button>
                    @endcan
                    @can('customers.delete')
                        <x-ui.icon-button tone="danger" x-data x-on:click="$dispatch('open-modal', 'delete-customer-{{ $customer->id }}')" aria-label="{{ __('Delete') }}">⌫</x-ui.icon-button>
                    @endcan
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>

    <div class="modals-root">
        @can('customers.create')
            <x-ui.dialog name="create-customer" :title="__('Add customer')" maxWidth="lg" :show="$errors->any() && old('_form') === 'customer-create'">
                <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="customer-create">
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('Identity & contact') }}</h3>
                        <div>
                            <label class="label">{{ __('Name') }}</label>
                            <input class="field" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mt-4">
                            <label class="label">{{ __('Phone') }}</label>
                            <input class="field" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div class="mt-4">
                            <label class="label">{{ __('Notes') }}</label>
                            <input class="field" name="notes" value="{{ old('notes') }}">
                        </div>
                    </section>
                    <div class="dialog-actions">
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'create-customer')">{{ __('Cancel') }}</button>
                        <button class="btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </x-ui.dialog>
        @endcan

        @foreach ($customers as $customer)
            @can('customers.update')
                <x-ui.dialog name="edit-customer-{{ $customer->id }}" :title="__('Edit :name', ['name' => $customer->name])" maxWidth="lg">
                    <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <section class="dialog-section">
                            <h3 class="dialog-section-title">{{ __('Identity & contact') }}</h3>
                            <div>
                                <label class="label">{{ __('Name') }}</label>
                                <input class="field" name="name" value="{{ $customer->name }}" required>
                            </div>
                            <div class="mt-4">
                                <label class="label">{{ __('Phone') }}</label>
                                <input class="field" name="phone" value="{{ $customer->phone }}">
                            </div>
                            <div class="mt-4">
                                <label class="label">{{ __('Notes') }}</label>
                                <textarea class="field" name="notes" rows="2">{{ $customer->notes }}</textarea>
                            </div>
                        </section>
                        <div class="dialog-actions">
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'edit-customer-{{ $customer->id }}')">{{ __('Cancel') }}</button>
                            <button class="btn-primary">{{ __('Update') }}</button>
                        </div>
                    </form>
                </x-ui.dialog>
            @endcan
            @can('customers.delete')
                <x-ui.dialog name="delete-customer-{{ $customer->id }}" :title="__('Delete customer?')" maxWidth="md">
                    <p class="text-sm text-bean dark:text-parchment/70">{{ __('Remove :name?', ['name' => $customer->name]) }}</p>
                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="mt-6 flex justify-end gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-customer-{{ $customer->id }}')">{{ __('Cancel') }}</button>
                        <button class="btn-danger">{{ __('Delete') }}</button>
                    </form>
                </x-ui.dialog>
            @endcan
        @endforeach
    </div>
</x-app-layout>
