<x-app-layout>
    <x-slot name="header">{{ __('Daily expenses') }}</x-slot>

    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="label" for="from">{{ __('From') }}</label>
                <input class="field" type="date" id="from" name="from" value="{{ $from }}">
            </div>
            <div>
                <label class="label" for="to">{{ __('To') }}</label>
                <input class="field" type="date" id="to" name="to" value="{{ $to }}">
            </div>
            <button class="btn-secondary">{{ __('Filter') }}</button>
        </form>

        @can('expenses.create')
            <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-expense')">
                {{ __('Add expense') }}
            </button>
        @endcan
    </div>

    <section class="panel overflow-hidden">
        <div class="hidden overflow-x-auto md:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Note') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $expense->category->name }}</div>
                                <div class="text-xs text-bean dark:text-parchment/50">{{ $expense->recorder->name }}</div>
                            </td>
                            <td>{{ $expense->note ?: '—' }}</td>
                            <td class="font-semibold">{{ \App\Support\Money::format($expense->amount) }}</td>
                            <td class="text-right">
                                @can('expenses.delete')
                                    <x-ui.icon-button tone="danger" x-data x-on:click="$dispatch('open-modal', 'delete-expense-{{ $expense->id }}')" aria-label="{{ __('Void expense') }}">⌫</x-ui.icon-button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-bean dark:text-parchment/55">{{ __('No expenses for this day.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-4 md:hidden">
            @forelse ($expenses as $expense)
                <article class="rounded-2xl border border-bean/10 bg-parchment-soft/60 p-4 dark:border-white/5 dark:bg-obsidian-soft">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="badge-soft">{{ $expense->category->name }}</span>
                            <div class="mt-2 text-sm">{{ $expense->note ?: '—' }}</div>
                        </div>
                        <div class="font-semibold">{{ \App\Support\Money::format($expense->amount) }}</div>
                    </div>
                    @can('expenses.delete')
                        <div class="mt-3 flex justify-end">
                            <button type="button" class="btn-ghost text-terracotta" x-data x-on:click="$dispatch('open-modal', 'delete-expense-{{ $expense->id }}')">{{ __('Void') }}</button>
                        </div>
                    @endcan
                </article>
            @empty
                <p class="py-8 text-center text-sm text-bean dark:text-parchment/55">{{ __('No expenses for this day.') }}</p>
            @endforelse
        </div>

        <div class="border-t border-bean/10 p-4 dark:border-white/5">{{ $expenses->links() }}</div>
    </section>

    <div class="modals-root">
        @can('expenses.create')
            <x-ui.dialog
                name="create-expense"
                :title="__('Add expense')"
                :subtitle="__('Selectable categories from your Maraki books.')"
                maxWidth="xl"
                :show="$errors->any() && old('_form') === 'expense'"
            >
                @include('expenses._form', ['categories' => $categories, 'date' => $date])
            </x-ui.dialog>
        @endcan

        @can('expenses.delete')
            @foreach ($expenses as $expense)
                <x-ui.dialog name="delete-expense-{{ $expense->id }}" :title="__('Void expense?')" maxWidth="md">
                    <p class="text-sm text-bean dark:text-parchment/70">
                        {{ __('Void :name — :amount?', ['name' => $expense->category->name, 'amount' => \App\Support\Money::format($expense->amount)]) }}
                    </p>
                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="mt-6 flex justify-end gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-expense-{{ $expense->id }}')">{{ __('Cancel') }}</button>
                        <button class="btn-danger">{{ __('Void') }}</button>
                    </form>
                </x-ui.dialog>
            @endforeach
        @endcan
    </div>
</x-app-layout>
