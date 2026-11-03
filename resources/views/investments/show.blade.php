<x-app-layout>
    <x-slot name="header">{{ $investment->investor->name }}</x-slot>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Type') }}</div>
            <div class="mt-2 font-display text-2xl">{{ $investment->type->label() }}</div>
        </div>
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Amount') }}</div>
            <div class="mt-2 font-display text-2xl">{{ \App\Support\Money::format($investment->amount) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-xs uppercase tracking-wide text-bean dark:text-parchment/55">{{ __('Outstanding') }}</div>
            <div class="mt-2 font-display text-2xl text-terracotta">{{ \App\Support\Money::format($investment->outstanding()) }}</div>
        </div>
    </div>

    @if ($investment->notes)
        <p class="mb-6 text-sm text-bean dark:text-parchment/60">{{ $investment->notes }}</p>
    @endif

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('investments.index') }}" class="btn-secondary">{{ __('Back') }}</a>
        @can('investments.update')
            @if ($investment->type->value === 'loan' && ! $investment->is_paid)
                <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'loan-payment')">{{ __('Record loan payment') }}</button>
            @endif
        @endcan
        @can('investments.delete')
            <button type="button" class="btn-danger" x-data x-on:click="$dispatch('open-modal', 'delete-investment')">{{ __('Delete') }}</button>
        @endcan
    </div>

    <section class="panel overflow-hidden">
        <div class="border-b border-bean/10 px-4 py-3 font-display text-lg dark:border-white/5">{{ __('Payment history') }}</div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('By') }}</th>
                        <th>{{ __('Note') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($investment->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M j, Y') }}</td>
                            <td>{{ \App\Support\Money::format($payment->amount) }}</td>
                            <td>{{ $payment->recorder->name }}</td>
                            <td>{{ $payment->note ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-bean dark:text-parchment/55">{{ __('No repayments yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modals-root">
        @can('investments.update')
            @if ($investment->type->value === 'loan' && ! $investment->is_paid)
                <x-ui.dialog name="loan-payment" :title="__('Record loan payment')" maxWidth="lg" :show="$errors->any() && old('_form') === 'loan-payment'">
                    <form method="POST" action="{{ route('loan-payments.store', $investment) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_form" value="loan-payment">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">{{ __('Date') }}</label>
                                <input class="field" type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required>
                            </div>
                            <div>
                                <label class="label">{{ __('Amount') }}</label>
                                <input class="field" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required>
                            </div>
                        </div>
                        <div>
                            <label class="label">{{ __('Note') }}</label>
                            <input class="field" name="note" value="{{ old('note') }}">
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'loan-payment')">{{ __('Cancel') }}</button>
                            <button class="btn-primary">{{ __('Save payment') }}</button>
                        </div>
                    </form>
                </x-ui.dialog>
            @endif
        @endcan

        @can('investments.delete')
            <x-ui.dialog name="delete-investment" :title="__('Delete investment?')" maxWidth="md">
                <p class="text-sm text-bean dark:text-parchment/70">{{ __('This cannot be undone from the books list.') }}</p>
                <form method="POST" action="{{ route('investments.destroy', $investment) }}" class="mt-6 flex justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'delete-investment')">{{ __('Cancel') }}</button>
                    <button class="btn-danger">{{ __('Delete') }}</button>
                </form>
            </x-ui.dialog>
        @endcan
    </div>
</x-app-layout>
