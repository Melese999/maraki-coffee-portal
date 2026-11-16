<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $menuItem = MenuItem::factory()->create();
        $quantity = fake()->numberBetween(1, 4);
        $unitPrice = (float) $menuItem->price;

        return [
            'order_id' => Order::factory(),
            'menu_item_id' => $menuItem->id,
            'item_name' => $menuItem->name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'line_total' => $unitPrice * $quantity,
        ];
    }
}
