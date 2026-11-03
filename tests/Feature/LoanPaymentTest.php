<?php

namespace Tests\Feature;

use App\Enums\InvestmentType;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_loan_marked_paid_after_full_repayment(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Manager');

        $investor = Investor::factory()->create();
        $investment = Investment::factory()->create([
            'investor_id' => $investor->id,
            'recorded_by' => $user->id,
            'type' => InvestmentType::Loan,
            'amount' => 1000,
            'is_paid' => false,
        ]);

        $this->actingAs($user)->post(route('loan-payments.store', $investment), [
            'payment_date' => today()->toDateString(),
            'amount' => 1000,
        ])->assertRedirect();

        $investment->refresh();
        $this->assertTrue($investment->is_paid);
        $this->assertSame(0.0, $investment->outstanding());
    }
}
