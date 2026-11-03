<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleItem>
 */
class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    public function definition(): array
    {
        $menuItem = MenuItem::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = (float) $menuItem->price;

        return [
            'sale_id' => Sale::factory(),
            'menu_item_id' => $menuItem->id,
            'item_name' => $menuItem->name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'line_total' => $unitPrice * $quantity,
        ];
    }
}
