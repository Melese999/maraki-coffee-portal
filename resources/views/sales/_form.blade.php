@props([
    'menuItems',
    'customers',
    'date' => null,
    'filterFrom' => null,
    'filterTo' => null,
])

<form
    method="POST"
    action="{{ route('sales.store') }}"
    class="space-y-4"
    x-data="{
        status: @js(old('payment_status', 'paid')),
        rows: [{ menu_item_id: '', quantity: 1 }]
    }"
>
    @csrf
    <input type="hidden" name="_form" value="sale">
    <input type="hidden" name="filter_from" value="{{ old('filter_from', $filterFrom ?? $date) }}">
    <input type="hidden" name="filter_to" value="{{ old('filter_to', $filterTo ?? $date) }}">

    <section class="dialog-section">
        <h3 class="dialog-section-title">{{ __('Sale details') }}</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="sale_date">{{ __('Date') }}</label>
                <input class="field" type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', $date ?? now()->toDateString()) }}" required>
            </div>
            <div>
                <label class="label">{{ __('Payment') }}</label>
                <div class="flex flex-wrap gap-2">
                    <label class="pill-option" :class="status === 'paid' && 'pill-option-active'">
                        <input type="radio" class="sr-only" name="payment_status" value="paid" x-model="status">
                        {{ __('Paid') }}
                    </label>
                    <label class="pill-option" :class="status === 'credit' && 'pill-option-active'">
                        <input type="radio" class="sr-only" name="payment_status" value="credit" x-model="status">
                        {{ __('Credit') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-4" x-show="status === 'credit'" x-cloak>
            <label class="label" for="customer_id">{{ __('Customer') }}</label>
            <select class="field" name="customer_id" id="customer_id">
                <option value="">{{ __('Select customer') }}</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}@if($customer->phone) · {{ $customer->phone }}@endif</option>
                @endforeach
            </select>
        </div>
    </section>

    <section class="dialog-section">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h3 class="dialog-section-title mb-0">{{ __('Menu items') }}</h3>
            <button type="button" class="btn-ghost text-xs" @click="rows.push({ menu_item_id: '', quantity: 1 })">+ {{ __('Add line') }}</button>
        </div>
        <div class="space-y-3">
            <template x-for="(row, index) in rows" :key="index">
                <div class="grid gap-3 sm:grid-cols-[1fr_110px_auto]">
                    <select class="field" :name="`items[${index}][menu_item_id]`" x-model="row.menu_item_id" required>
                        <option value="">{{ __('Choose item') }}</option>
                        @foreach ($menuItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} — {{ \App\Support\Money::format($item->price) }} / {{ $item->unit }}</option>
                        @endforeach
                    </select>
                    <input class="field" type="number" min="1" :name="`items[${index}][quantity]`" x-model="row.quantity" required>
                    <button type="button" class="btn-secondary" @click="rows.length > 1 && rows.splice(index, 1)" x-show="rows.length > 1">{{ __('Remove') }}</button>
                </div>
            </template>
        </div>
    </section>

    <section class="dialog-section">
        <h3 class="dialog-section-title">{{ __('Notes') }}</h3>
        <input class="field" type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="{{ __('Optional note') }}">
    </section>

    <div class="dialog-actions">
        <button type="button" class="btn-secondary" x-on:click="$dispatch('close-modal', 'create-sale')">{{ __('Cancel') }}</button>
        <button class="btn-primary">{{ __('Save sale') }}</button>
    </div>
</form>
