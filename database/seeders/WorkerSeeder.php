<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos trabajadores de ejemplo
        $workers = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'worker',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'María García',
                'email' => 'maria.garcia@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'worker',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos López',
                'email' => 'carlos.lopez@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'worker',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ana Rodríguez',
                'email' => 'ana.rodriguez@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'worker',
                'email_verified_at' => null, // Inactivo
            ],
            [
                'name' => 'Pedro Martínez',
                'email' => 'pedro.martinez@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'worker',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($workers as $worker) {
            User::create($worker);
        }
    }
}