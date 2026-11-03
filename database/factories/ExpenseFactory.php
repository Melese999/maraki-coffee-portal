<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'expense_category_id' => ExpenseCategory::factory(),
            'recorded_by' => User::factory(),
            'expense_date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 50, 5000),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
