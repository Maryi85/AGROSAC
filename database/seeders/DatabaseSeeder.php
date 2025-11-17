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
        // Usuario administrador
        User::query()->updateOrCreate(
            ['email' => 'perdomomaryii06@gmail.com'],
            ['name' => 'Admin', 'password' => bcrypt('Adminagrosac123'), 'role' => 'admin']
        );
    }
}
