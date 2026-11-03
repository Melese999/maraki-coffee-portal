<?php

namespace Database\Factories;

use App\Enums\InvestmentType;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Investment>
 */
class InvestmentFactory extends Factory
{
    protected $model = Investment::class;

    public function definition(): array
    {
        return [
            'investor_id' => Investor::factory(),
            'recorded_by' => User::factory(),
            'type' => InvestmentType::Loan,
            'amount' => fake()->randomFloat(2, 1000, 100000),
            'invested_on' => fake()->date(),
            'is_paid' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
