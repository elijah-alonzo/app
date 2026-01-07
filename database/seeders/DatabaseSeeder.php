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
        // Seed roles, permissions, departments and admin user
        $this->call([
            RolePermissionSeeder::class,
            PresentationSeeder::class,
        ]);

        // Create single admin user
        User::factory()->create([
            'school_number' => '2024-000001',
            'name' => 'Administrator',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('Admin');
    }
}
