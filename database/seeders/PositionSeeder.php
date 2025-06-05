<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Executive Director',
                'level' => 5,
                'salary_min' => 7000,
                'salary_max' => 12000,
                'description' => 'Handles overall operations'
            ],
            [
                'name' => 'District Coodinator',
                'level' => 5,
                'salary_min' => 7000,
                'salary_max' => 12000,
                'description' => 'Handles overall district operations'
            ],
            [
                'name' => 'District Supervisor',
                'level' => 4,
                'salary_min' => 7000,
                'salary_max' => 12000,
                'description' => 'Leads district personnel'
            ],
            [
                'name' => 'HR Manager',
                'level' => 4,
                'salary_min' => 7000,
                'salary_max' => 12000,
                'description' => 'Leads human resources department'
            ],
            [
                'name' => 'IT Personnel',
                'level' => 4,
                'salary_min' => 8000,
                'salary_max' => 15000,
                'description' => 'Provides information technology support'
            ],
            [
                'name' => 'Office Staff',
                'level' => 1,
                'salary_min' => 1800,
                'salary_max' => 3000,
                'description' => 'Provides administrative support'
            ],
            [
                'name' => 'Company Driver',
                'level' => 1,
                'salary_min' => 1800,
                'salary_max' => 3000,
                'description' => 'Provides transportation services'
            ],
            [
                'name' => 'Tent Installer',
                'level' => 1,
                'salary_min' => 1800,
                'salary_max' => 3000,
                'description' => 'Provides tent setup services'
            ],
            [
                'name' => 'Utility Personnel',
                'level' => 1,
                'salary_min' => 1800,
                'salary_max' => 3000,
                'description' => 'Maintains day-to-day cleanliness'
            ],
            [
                'name' => 'Kitchen Staff',
                'level' => 1,
                'salary_min' => 1800,
                'salary_max' => 3000,
                'description' => 'Handels food preparation'
            ],
            [
                'name' => 'Security Personnel',
                'level' => 1,
                'salary_min' => 1800,
                'salary_max' => 3000,
                'description' => 'Provides day-to-day safety and security services'
            ]
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
