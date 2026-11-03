<?php

namespace Database\Factories;

use App\Models\Investment;
use App\Models\LoanPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanPayment>
 */
class LoanPaymentFactory extends Factory
{
    protected $model = LoanPayment::class;

    public function definition(): array
    {
        return [
            'investment_id' => Investment::factory(),
            'recorded_by' => User::factory(),
            'payment_date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
