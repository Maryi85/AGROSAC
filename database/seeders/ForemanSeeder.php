<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ForemanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos mayordomos de ejemplo
        $foremen = [
            [
                'name' => 'Carlos Mendoza',
                'email' => 'carlos.mendoza@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'foreman',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'foreman',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'José Ramírez',
                'email' => 'jose.ramirez@sacro.com',
                'password' => Hash::make('password123'),
                'role' => 'foreman',
                'email_verified_at' => null, // Inactivo
            ],
        ];

        foreach ($foremen as $foreman) {
            User::create($foreman);
        }
    }
}