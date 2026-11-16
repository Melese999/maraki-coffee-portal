<?php

namespace App\Http\Controllers\Storefront;

use App\Enums\FulfillmentType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StorePublicOrderRequest;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(): View
    {
        return view('storefront.order', [
            'menuItems' => MenuItem::query()->active()->ordered()->get(),
        ]);
    }

    public function store(StorePublicOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $fulfillment = FulfillmentType::from($validated['fulfillment_type']);

        $order = DB::transaction(function () use ($validated, $fulfillment): Order {
            $lines = collect($validated['items'])->map(function (array $line) {
                $menuItem = MenuItem::query()
                    ->active()
                    ->whereKey($line['menu_item_id'])
                    ->firstOrFail();

                $quantity = (int) $line['quantity'];
                $unitPrice = (float) $menuItem->price;

                return [
                    'menu_item' => $menuItem,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $unitPrice * $quantity,
                ];
            });

            $subtotal = round((float) $lines->sum('line_total'), 2);

            $order = Order::query()->create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'fulfillment_type' => $fulfillment,
                'delivery_address' => $fulfillment === FulfillmentType::Delivery
                    ? ($validated['delivery_address'] ?? null)
                    : null,
                'notes' => $validated['notes'] ?? null,
                'status' => OrderStatus::Pending,
                'payment_status' => OrderPaymentStatus::Unpaid,
                'payment_method' => 'pay_at_shop',
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            foreach ($lines as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'menu_item_id' => $line['menu_item']->id,
                    'item_name' => $line['menu_item']->name,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);
            }

            return $order;
        });

        return redirect()
            ->route('storefront.orders.show', $order->order_number)
            ->with('success', __('Order placed. Pay at the shop when you collect or receive it.'));
    }

    public function show(string $orderNumber): View
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('storefront.confirmation', [
            'order' => $order,
        ]);
    }
}
