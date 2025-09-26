<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'external_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pending', 'successful', 'failed']),
            'method' => $this->faker->randomElement(['credit_card', 'paypal']),
            'amount' => $this->faker->numberBetween(100, 100000),
            'metadata' => [
                'attempt' => 1,
            ],
        ];
    }
}
