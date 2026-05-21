<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@petitpoto.ci'],
            [
                'name' => 'Admin Principal',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'organizer@petitpoto.ci'],
            [
                'name' => 'Organisateur Test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ORGANIZER,
            ]
        );
    }
}