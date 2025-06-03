<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'last_active_at' => now()
            ]
        );

        // Crear usuario artista
        User::firstOrCreate(
            ['email' => 'artist@example.com'],
            [
                'name' => 'Artista',
                'password' => Hash::make('password'),
                'role' => 'artist',
                'is_active' => true,
                'email_verified_at' => now(),
                'last_active_at' => now()
            ]
        );
    }
}
