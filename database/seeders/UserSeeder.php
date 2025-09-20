<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        \App\Models\User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
        ]);

        \App\Models\User::create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
