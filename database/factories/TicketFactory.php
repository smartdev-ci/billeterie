<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Services\QR\QRCodeService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $uuid = (string) Str::uuid();
        $qrService = app(QRCodeService::class);

        return [
            'uuid' => $uuid,
            'order_id' => \App\Models\Order::factory(),
            'event_id' => \App\Models\Event::factory(),
            'customer_email' => fake()->unique()->safeEmail(),
            'status' => 'valid',
            // Valeurs par défaut pour les tests (QR simulé)
            'qr_code' => 'data:image/svg+xml;base64,' . base64_encode('<svg>dummy</svg>'),
            'qr_signature' => $qrService->sign($uuid),
        ];
    }

    /**
     * Indique que le ticket a déjà été utilisé
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    /**
     * Indique que le ticket a été annulé
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}