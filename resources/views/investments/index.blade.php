<x-app-layout>
    <x-slot name="header">{{ __('Investments & loans') }}</x-slot>

    <div class="mb-5 flex flex-wrap justify-end gap-2">
        @can('investments.create')
            <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('open-modal', 'create-investor')">{{ __('Add lender') }}</button>
            <button type="button" class="btn-primary" x-data x-on:click="$dispatch('open-modal', 'create-investment')">{{ __('Record investment / loan') }}</button>
        @endcan
    </div>

    <section class="panel overflow-hidden">
        <div class="hidden overflow-x-auto md:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('From') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Outstanding') }}</th>
                        <th>{{ __('Paid?') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($investments as $investment)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $investment->investor->name }}</div>
                                <div class="text-xs text-bean dark:text-parchment/50">{{ $investment->invested_on->format('M j, Y') }}</div>
                            </td>
                            <td><span class="badge-soft">{{ $investment->type->label() }}</span></td>
                            <td>{{ \App\Support\Money::format($investment->amount) }}</td>
                            <td>{{ \App\Support\Money::format($investment->outstanding()) }}</td>
                            <td>
                                @if ($investment->type->value === 'equity')
                                    —
                                @else
                                    <span @class([$investment->is_paid ? 'badge-paid' : 'badge-credit'])>
                                        {{ $investment->is_paid ? __('Yes') : __('No') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('investments.show', $investment) }}" class="btn-icon" aria-label="{{ __('Open') }}">→</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-bean dark:text-parchment/55">{{ __('No investments yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-4 md:hidden">
            @forelse ($investments as $investment)
                <a href="{{ route('investments.show', $investment) }}" class="block rounded-2xl border border-bean/10 bg-parchment-soft/60 p-4 dark:border-white/5 dark:bg-obsidian-soft">
                    <div class="flex justify-between gap-3">
                        <div>
                            <div class="font-semibold">{{ $investment->investor->name }}</div>
                            <span class="badge-soft mt-2 inline-flex">{{ $investment->type->label() }}</span>
                        </div>
                        <div class="text-right text-sm">
                            <div class="font-semibold">{{ \App\Support\Money::format($investment->amount) }}</div>
                            <div class="text-bean dark:text-parchment/50">{{ __('due :amount', ['amount' => \App\Support\Money::format($investment->outstanding())]) }}</div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="py-8 text-center text-sm text-bean dark:text-parchment/55">{{ __('No investments yet.') }}</p>
            @endforelse
        </div>

        <div class="border-t border-bean/10 p-4 dark:border-white/5">{{ $investments->links() }}</div>
    </section>

    <div class="modals-root">
        @can('investments.create')
            <x-ui.dialog name="create-investor" :title="__('Add lender / investor')" maxWidth="lg" :show="$errors->any() && old('_form') === 'investor'">
                <form method="POST" action="{{ route('investors.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="investor">
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
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'create-investor')">{{ __('Cancel') }}</button>
                        <button class="btn-primary">{{ __('Save person') }}</button>
                    </div>
                </form>
            </x-ui.dialog>

            <x-ui.dialog name="create-investment" :title="__('Record investment / loan')" maxWidth="xl" :show="$errors->any() && old('_form') === 'investment'">
                <form method="POST" action="{{ route('investments.store') }}" class="space-y-4" x-data="{ type: '{{ old('type', 'loan') }}' }">
                    @csrf
                    <input type="hidden" name="_form" value="investment">
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('From who') }}</h3>
                        <select class="field" name="investor_id" required>
                            <option value="">{{ __('Select person') }}</option>
                            @foreach ($investors as $investor)
                                <option value="{{ $investor->id }}" @selected(old('investor_id') == $investor->id)>{{ $investor->name }}</option>
                            @endforeach
                        </select>
                        <div class="mt-4">
                            <label class="label">{{ __('Type') }}</label>
                            <div class="flex flex-wrap gap-2">
                                <label class="pill-option" :class="type === 'loan' && 'pill-option-active'">
                                    <input type="radio" class="sr-only" name="type" value="loan" x-model="type"> {{ __('Loan') }}
                                </label>
                                <label class="pill-option" :class="type === 'equity' && 'pill-option-active'">
                                    <input type="radio" class="sr-only" name="type" value="equity" x-model="type"> {{ __('Equity') }}
                                </label>
                            </div>
                        </div>
                    </section>
                    <section class="dialog-section">
                        <h3 class="dialog-section-title">{{ __('Amount & date') }}</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">{{ __('Amount') }}</label>
                                <input class="field" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required>
                            </div>
                            <div>
                                <label class="label">{{ __('Date') }}</label>
                                <input class="field" type="date" name="invested_on" value="{{ old('invested_on', now()->toDateString()) }}" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="label">{{ __('Notes') }}</label>
                            <input class="field" name="notes" value="{{ old('notes') }}">
                        </div>
                    </section>
                    <div class="dialog-actions">
                        <button type="button" class="btn-secondary" x-data x-on:click="$dispatch('close-modal', 'create-investment')">{{ __('Cancel') }}</button>
                        <button class="btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </x-ui.dialog>
        @endcan
    </div>
</x-app-layout>
