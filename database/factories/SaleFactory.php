<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 30, 300);

        return [
            'recorded_by' => User::factory(),
            'customer_id' => null,
            'sale_date' => fake()->date(),
            'payment_status' => PaymentStatus::Paid,
            'total' => $total,
            'amount_paid' => $total,
            'notes' => null,
        ];
    }

    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatus::Credit,
            'amount_paid' => 0,
        ]);
    }
}
