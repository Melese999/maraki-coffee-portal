<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\MenuItem;
use App\Models\User;
use App\Support\DailyProfitCalculator;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleAndProfitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_cashier_can_record_paid_sale_and_profit_uses_cash_collected(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $item = MenuItem::factory()->create(['price' => 50, 'is_active' => true]);

        $this->actingAs($user)->post(route('sales.store'), [
            'sale_date' => today()->toDateString(),
            'payment_status' => PaymentStatus::Paid->value,
            'items' => [
                ['menu_item_id' => $item->id, 'quantity' => 2],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'total' => 100,
            'amount_paid' => 100,
            'payment_status' => 'paid',
        ]);

        $stats = app(DailyProfitCalculator::class)->forDate(today());
        $this->assertSame(100.0, $stats['cash_sales']);
        $this->assertSame(100.0, $stats['cash_collected']);
    }
}
