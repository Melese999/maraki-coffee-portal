<div class="flex flex-wrap justify-end gap-2">
    @foreach ($order->status->nextStatuses() as $next)
        <form method="POST" action="{{ route('orders.update', $order) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $next->value }}">
            <button class="btn-secondary !px-3 !py-1.5 text-xs">{{ $next->label() }}</button>
        </form>
    @endforeach

    @if ($order->payment_status->value !== 'paid' && $order->status->value !== 'cancelled')
        <form method="POST" action="{{ route('orders.update', $order) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="payment_status" value="paid">
            <button class="btn-secondary !px-3 !py-1.5 text-xs">{{ __('Mark paid') }}</button>
        </form>
    @endif

    @if ($order->canConvertToSale())
        <form method="POST" action="{{ route('orders.update', $order) }}" onsubmit="return confirm(@js(__('Record this order as a paid sale?')))">
            @csrf
            @method('PATCH')
            <input type="hidden" name="convert_to_sale" value="1">
            <button class="btn-primary !px-3 !py-1.5 text-xs">{{ __('Record as sale') }}</button>
        </form>
    @endif
</div>
