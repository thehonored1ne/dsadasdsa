<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Program Chair
        User::factory()->programChair()->create([
            'name' => 'Program Chair',
            'email' => 'chair@school.edu',
            'password' => bcrypt('password'),
        ]);

        // Teacher
        User::factory()->create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@school.edu',
            'password' => bcrypt('password'),
        ]);
    }
}