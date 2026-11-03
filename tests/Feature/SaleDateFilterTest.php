<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleDateFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_sales_index_filters_by_from_to_sale_date(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $inRange = Sale::factory()->create([
            'recorded_by' => $user->id,
            'sale_date' => today()->subDay()->toDateString(),
            'total' => 40,
            'amount_paid' => 40,
        ]);

        $outOfRange = Sale::factory()->create([
            'recorded_by' => $user->id,
            'sale_date' => today()->subDays(5)->toDateString(),
            'total' => 55,
            'amount_paid' => 55,
        ]);

        $this->actingAs($user)
            ->get(route('sales.index', [
                'from' => today()->subDays(2)->toDateString(),
                'to' => today()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee((string) $inRange->total)
            ->assertDontSee((string) $outOfRange->total)
            ->assertSee($inRange->sale_date->translatedFormat('M j, Y'));
    }

    public function test_recording_sale_redirects_to_index_with_date_filter(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $item = MenuItem::factory()->create(['price' => 50, 'is_active' => true]);
        $saleDate = today()->subDay()->toDateString();

        $this->actingAs($user)
            ->post(route('sales.store'), [
                'sale_date' => $saleDate,
                'payment_status' => 'paid',
                'filter_from' => today()->subDays(3)->toDateString(),
                'filter_to' => today()->toDateString(),
                'items' => [
                    ['menu_item_id' => $item->id, 'quantity' => 1],
                ],
            ])
            ->assertRedirect(route('sales.index', [
                'from' => today()->subDays(3)->toDateString(),
                'to' => today()->toDateString(),
            ]));
    }
}
