<x-app-layout>
    <x-slot name="header">{{ __('Activity log') }}</x-slot>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label class="label" for="from">{{ __('From') }}</label>
            <input class="field" type="date" name="from" id="from" value="{{ request('from') }}">
        </div>
        <div>
            <label class="label" for="to">{{ __('To') }}</label>
            <input class="field" type="date" name="to" id="to" value="{{ request('to') }}">
        </div>
        <button class="btn-secondary">{{ __('Filter') }}</button>
    </form>

    <section class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('When') }}</th>
                        <th>{{ __('Who') }}</th>
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('Subject') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="whitespace-nowrap">{{ $activity->created_at->format('M j, Y H:i') }}</td>
                            <td>{{ $activity->causer?->name ?? __('System') }}</td>
                            <td><span class="badge-soft">{{ $activity->description }}</span></td>
                            <td>
                                {{ class_basename($activity->subject_type ?? 'n/a') }}
                                @if ($activity->subject_id)
                                    #{{ $activity->subject_id }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-bean dark:text-parchment/55">{{ __('No activity yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-bean/10 p-4 dark:border-white/5">{{ $activities->links() }}</div>
    </section>
</x-app-layout>
