<?php

namespace Database\Factories;

use App\Models\CreditPayment;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditPayment>
 */
class CreditPaymentFactory extends Factory
{
    protected $model = CreditPayment::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory()->credit(),
            'recorded_by' => User::factory(),
            'payment_date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 10, 100),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
