<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuarios de prueba por rol
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        User::query()->updateOrCreate(
            ['email' => 'foreman@example.com'],
            ['name' => 'Foreman', 'password' => bcrypt('password'), 'role' => 'foreman']
        );

        User::query()->updateOrCreate(
            ['email' => 'worker@example.com'],
            ['name' => 'Worker', 'password' => bcrypt('password'), 'role' => 'worker']
        );

        // Ejecutar seeder de mayordomos
        $this->call(ForemanSeeder::class);
        
        // Ejecutar seeder de trabajadores
        $this->call(WorkerSeeder::class);
    }
}
