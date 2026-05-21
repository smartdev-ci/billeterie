<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminOrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::updateOrCreate(
            ['email' => 'admin@petitpoto.ci'],
            [
                'name' => 'Admin Principal',
                'password' => Hash::make('PetitPoto2026!'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        
        User::updateOrCreate(
            ['email' => 'organizer@petitpoto.ci'],
            [
                'name' => 'Organisateur Principal',
                'password' => Hash::make('PetitPoto2026!'),
                'role' => User::ROLE_ORGANIZER,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        
        User::updateOrCreate(
            ['email' => 'scan@petitpoto.ci'],
            [
                'name' => 'Agent Scan',
                'password' => Hash::make('Scan2026!'),
                'role' => User::ROLE_ORGANIZER,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        
        User::updateOrCreate(
            ['email' => 'user@petitpoto.ci'],
            [
                'name' => 'Utilisateur Test',
                'password' => Hash::make('User2026!'),
                'role' => User::ROLE_USER,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $this->command->info('Comptes staff créés :');
        $this->command->line('admin@petitpoto.ci / PetitPoto2026!  [ROLE: admin]');
        $this->command->line('organizer@petitpoto.ci / PetitPoto2026!  [ROLE: organizer]');
        $this->command->line('scan@petitpoto.ci / Scan2026!  [ROLE: organizer]');
        $this->command->line('user@petitpoto.ci / User2026!  [ROLE: user]');
        $this->command->warn('Changez les mots de passe en production !');
    }
}