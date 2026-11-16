<?php

namespace Tests\Feature;

use App\Enums\FulfillmentType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_landing_and_order_pages(): void
    {
        MenuItem::factory()->create(['name' => 'Bunna', 'is_active' => true]);

        $this->get(route('storefront.home'))->assertOk();
        $this->get(route('storefront.order'))
            ->assertOk()
            ->assertSee('Bunna');
    }

    public function test_guest_can_place_pickup_order(): void
    {
        $item = MenuItem::factory()->create(['price' => 40, 'is_active' => true]);

        $response = $this->post(route('storefront.order.store'), [
            'customer_name' => 'Sara',
            'customer_phone' => '0911223344',
            'fulfillment_type' => FulfillmentType::Pickup->value,
            'items' => [
                ['menu_item_id' => $item->id, 'quantity' => 2],
            ],
        ]);

        $order = Order::query()->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('storefront.orders.show', $order->order_number));

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Sara',
            'fulfillment_type' => 'pickup',
            'status' => OrderStatus::Pending->value,
            'payment_status' => OrderPaymentStatus::Unpaid->value,
            'payment_method' => 'pay_at_shop',
            'total' => 80,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_item_id' => $item->id,
            'quantity' => 2,
            'line_total' => 80,
        ]);
    }

    public function test_guest_can_place_delivery_order(): void
    {
        $item = MenuItem::factory()->create(['price' => 55, 'is_active' => true]);

        $this->post(route('storefront.order.store'), [
            'customer_name' => 'Abel',
            'customer_phone' => '0911000000',
            'fulfillment_type' => FulfillmentType::Delivery->value,
            'delivery_address' => 'Bole, Addis Ababa',
            'items' => [
                ['menu_item_id' => $item->id, 'quantity' => 1],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Abel',
            'fulfillment_type' => 'delivery',
            'delivery_address' => 'Bole, Addis Ababa',
            'total' => 55,
        ]);
    }

    public function test_delivery_order_requires_address(): void
    {
        $item = MenuItem::factory()->create(['is_active' => true]);

        $this->from(route('storefront.order'))
            ->post(route('storefront.order.store'), [
                'customer_name' => 'Abel',
                'customer_phone' => '0911000000',
                'fulfillment_type' => FulfillmentType::Delivery->value,
                'items' => [
                    ['menu_item_id' => $item->id, 'quantity' => 1],
                ],
            ])
            ->assertRedirect(route('storefront.order'))
            ->assertSessionHasErrors('delivery_address');
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_order_confirmation_page_is_public(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'MK-TEST-0001',
            'customer_name' => 'Hanna',
        ]);

        $this->get(route('storefront.orders.show', 'MK-TEST-0001'))
            ->assertOk()
            ->assertSee('MK-TEST-0001')
            ->assertSee('Hanna');
    }
}
