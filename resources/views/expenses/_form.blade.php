@props([
    'categories',
    'date' => null,
])

<form method="POST" action="{{ route('expenses.store') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="_form" value="expense">

    <div>
        <label class="label" for="expense_category_id">{{ __('Category') }}</label>
        <div class="flex flex-wrap gap-2" x-data="{ selected: '{{ old('expense_category_id', $categories->first()?->id) }}' }">
            @foreach ($categories as $category)
                <label class="pill-option" :class="selected == '{{ $category->id }}' && 'pill-option-active'">
                    <input type="radio" class="sr-only" name="expense_category_id" value="{{ $category->id }}" x-model="selected" @checked(old('expense_category_id', $categories->first()?->id) == $category->id)>
                    {{ $category->name }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="label" for="expense_date">{{ __('Date') }}</label>
            <input class="field" type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $date ?? now()->toDateString()) }}" required>
        </div>
        <div>
            <label class="label" for="amount">{{ __('Amount') }}</label>
            <input class="field" type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount') }}" required>
        </div>
    </div>

    <div>
        <label class="label" for="note">{{ __('Note') }}</label>
        <input class="field" type="text" name="note" id="note" value="{{ old('note') }}">
    </div>

    <div class="flex flex-wrap justify-end gap-2 border-t border-bean/10 pt-4 dark:border-white/10">
        <button type="button" class="btn-secondary" x-on:click="$dispatch('close-modal', 'create-expense')">{{ __('Cancel') }}</button>
        <button class="btn-primary">{{ __('Save expense') }}</button>
    </div>
</form>
