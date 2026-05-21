<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::updateOrCreate(
            ['id' => 1], // Force l'unicité absolue
            [
                'name' => 'Le Petit Poto - Édition 1',
                'description' => 'Une nuit immersive au cœur d\'Abidjan. Basses, lumières et vibes afro-urbaines.',
                'event_date' => now()->addDays(45)->setTime(20, 0),
                'location' => 'Abidjan, Plateau - Salle Éphémère',
                'max_tickets' => 1500,
                'tickets_sold' => 0,
                'status' => 'active',
            ]
        );
    }
}