<?php

namespace Database\Factories;

use App\Enums\FulfillmentType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 40, 400);

        return [
            'order_number' => 'MK-'.fake()->unique()->bothify('######??'),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->numerify('09########'),
            'fulfillment_type' => FulfillmentType::Pickup,
            'delivery_address' => null,
            'notes' => null,
            'status' => OrderStatus::Pending,
            'payment_status' => OrderPaymentStatus::Unpaid,
            'payment_method' => 'pay_at_shop',
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'sale_id' => null,
        ];
    }

    public function delivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'fulfillment_type' => FulfillmentType::Delivery,
            'delivery_address' => fake()->streetAddress().', Addis Ababa',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => OrderPaymentStatus::Paid,
        ]);
    }
}
