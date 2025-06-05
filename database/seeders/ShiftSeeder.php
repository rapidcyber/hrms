<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Morning Shift',
                'time_in' => '08:00:00',
                'time_out' => '17:00:00',
                'rest_days' => json_encode([0, 6]), // Assuming 0 = Sunday, 6 = Saturday
            ],
            [
                'name' => 'Day Shift',
                'time_in' => '06:00:00',
                'time_out' => '18:00:00',
                'rest_days' => json_encode([0, 6]), // Assuming 0 = Sunday, 6 = Saturday
            ],
            [
                'name' => 'Night Shift',
                'time_in' => '18:00:00',
                'time_out' => '06:00:00',
                'rest_days' => json_encode([0, 6]),
            ],

        ];
        foreach ($data as $shift) {
            \DB::table('shifts')->insert([
                'name' => $shift['name'],
                'time_in' => $shift['time_in'],
                'time_out' => $shift['time_out'],
            ]);
        }
    }
}
