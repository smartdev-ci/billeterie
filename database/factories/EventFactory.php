<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'event_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'location' => fake()->city() . ', ' . fake()->countryCode(),
            'max_tickets' => 1000,
            'tickets_sold' => 0,
            'status' => 'active',
        ];
    }
}