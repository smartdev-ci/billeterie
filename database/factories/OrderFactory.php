<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->unique()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'quantity' => fake()->numberBetween(1, 5),
            'total_amount' => Order::TICKET_PRICE * fake()->numberBetween(1, 5),
            'payment_status' => 'completed',
            'mobile_provider' => fake()->randomElement(['mtn', 'orange', 'moov']),
        ];
    }
}