<?php

namespace App\Http\Controllers;

use App\Enums\FulfillmentType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderToSaleConverter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $status = $request->string('status')->toString();
        $fulfillment = $request->string('fulfillment')->toString();

        $orders = Order::query()
            ->with('items')
            ->when(
                $status !== '' && OrderStatus::tryFrom($status),
                fn ($query) => $query->where('status', $status),
            )
            ->when(
                $fulfillment !== '' && FulfillmentType::tryFrom($fulfillment),
                fn ($query) => $query->where('fulfillment_type', $fulfillment),
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $openCount = Order::query()
            ->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Confirmed->value,
                OrderStatus::Preparing->value,
                OrderStatus::Ready->value,
            ])
            ->count();

        return view('orders.index', [
            'orders' => $orders,
            'status' => $status,
            'fulfillment' => $fulfillment,
            'openCount' => $openCount,
            'statuses' => OrderStatus::cases(),
            'fulfillmentTypes' => FulfillmentType::cases(),
        ]);
    }

    public function update(
        UpdateOrderRequest $request,
        Order $order,
        OrderToSaleConverter $converter,
    ): RedirectResponse {
        $this->authorize('update', $order);

        $validated = $request->validated();

        if ($request->boolean('convert_to_sale')) {
            try {
                $converter->convert($order, $request->user());
            } catch (InvalidArgumentException $exception) {
                return back()->with('error', $exception->getMessage());
            }

            return back()->with('success', __('Order recorded as a sale.'));
        }

        if (isset($validated['status'])) {
            $order->status = OrderStatus::from($validated['status']);
        }

        if (isset($validated['payment_status'])) {
            $order->payment_status = OrderPaymentStatus::from($validated['payment_status']);
        }

        $order->save();

        return back()->with('success', __('Order updated.'));
    }
}
