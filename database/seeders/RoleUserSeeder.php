<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign roles to users
        \DB::table('role_user')->insert([
            [
            'user_id' => 1,
            'role_id' => 1,
            ],
            [
            'user_id' => 2,
            'role_id' => 2,
            ],
            // Add more user-role assignments as needed
        ]);
    }
}
