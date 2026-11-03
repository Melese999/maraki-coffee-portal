<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Support\DailyProfitCalculator;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditCollectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_credit_sale_is_excluded_until_collected(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $customer = Customer::factory()->create();
        $item = MenuItem::factory()->create(['price' => 55]);

        $sale = Sale::factory()->credit()->create([
            'recorded_by' => $user->id,
            'customer_id' => $customer->id,
            'sale_date' => today(),
            'total' => 110,
            'amount_paid' => 0,
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'menu_item_id' => $item->id,
            'item_name' => $item->name,
            'unit_price' => 55,
            'quantity' => 2,
            'line_total' => 110,
        ]);

        $before = app(DailyProfitCalculator::class)->forDate(today());
        $this->assertSame(0.0, $before['cash_collected']);

        $this->actingAs($user)->post(route('credit.payments.store'), [
            'sale_id' => $sale->id,
            'payment_date' => today()->toDateString(),
            'amount' => 110,
        ])->assertRedirect();

        $sale->refresh();
        $this->assertTrue($sale->isFullyPaid());
        $this->assertSame(PaymentStatus::Paid, $sale->payment_status);

        $after = app(DailyProfitCalculator::class)->forDate(today());
        $this->assertSame(110.0, $after['credit_collections']);
        $this->assertSame(110.0, $after['cash_collected']);
    }
}
