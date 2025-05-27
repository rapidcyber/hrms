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
                'time_out' => '16:00:00',
            ],
            [
                'name' => 'Evening Shift',
                'time_in' => '16:00:00',
                'time_out' => '00:00:00',
            ],
            [
                'name' => 'Night Shift',
                'time_in' => '00:00:00',
                'time_out' => '08:00:00',
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
