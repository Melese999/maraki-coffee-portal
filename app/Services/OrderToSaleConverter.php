<?php

namespace App\Services;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderToSaleConverter
{
    public function convert(Order $order, User $recorder): Sale
    {
        if (! $order->canConvertToSale()) {
            throw new InvalidArgumentException(__('This order cannot be recorded as a sale.'));
        }

        return DB::transaction(function () use ($order, $recorder): Sale {
            $order->loadMissing('items');

            if ($order->items->contains(fn ($item) => $item->menu_item_id === null)) {
                throw new InvalidArgumentException(__('This order cannot be recorded as a sale.'));
            }

            $sale = Sale::query()->create([
                'recorded_by' => $recorder->id,
                'customer_id' => null,
                'sale_date' => today()->toDateString(),
                'payment_status' => PaymentStatus::Paid,
                'total' => $order->total,
                'amount_paid' => $order->total,
                'notes' => trim(implode(' · ', array_filter([
                    __('Online order :number', ['number' => $order->order_number]),
                    $order->notes,
                ]))),
            ]);

            foreach ($order->items as $item) {
                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'menu_item_id' => $item->menu_item_id,
                    'item_name' => $item->item_name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ]);
            }

            $order->forceFill([
                'sale_id' => $sale->id,
                'payment_status' => OrderPaymentStatus::Paid,
                'status' => OrderStatus::Completed,
            ])->save();

            return $sale;
        });
    }
}
