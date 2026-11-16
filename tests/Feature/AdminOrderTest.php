<?php

namespace Tests\Feature;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_cashier_can_list_orders_and_update_status(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $order = Order::factory()->create([
            'status' => OrderStatus::Pending,
        ]);

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);

        $this->actingAs($user)
            ->patch(route('orders.update', $order), [
                'status' => OrderStatus::Confirmed->value,
            ])
            ->assertRedirect();

        $this->assertSame(OrderStatus::Confirmed, $order->fresh()->status);
    }

    public function test_staff_can_convert_order_to_sale(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Manager');

        $item = MenuItem::factory()->create(['price' => 50, 'is_active' => true]);
        $order = Order::factory()->create([
            'status' => OrderStatus::Ready,
            'payment_status' => OrderPaymentStatus::Unpaid,
            'subtotal' => 100,
            'total' => 100,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'menu_item_id' => $item->id,
            'item_name' => $item->name,
            'unit_price' => 50,
            'quantity' => 2,
            'line_total' => 100,
        ]);

        $this->actingAs($user)
            ->patch(route('orders.update', $order), [
                'convert_to_sale' => true,
            ])
            ->assertRedirect();

        $order->refresh();
        $this->assertNotNull($order->sale_id);
        $this->assertSame(OrderPaymentStatus::Paid, $order->payment_status);
        $this->assertSame(OrderStatus::Completed, $order->status);

        $this->assertDatabaseHas('sales', [
            'id' => $order->sale_id,
            'total' => 100,
            'amount_paid' => 100,
            'payment_status' => PaymentStatus::Paid->value,
            'recorded_by' => $user->id,
        ]);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $order->sale_id,
            'menu_item_id' => $item->id,
            'quantity' => 2,
            'line_total' => 100,
        ]);

        $this->assertSame(1, Sale::query()->count());
    }

    public function test_user_without_permission_cannot_view_orders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertForbidden();
    }
}
