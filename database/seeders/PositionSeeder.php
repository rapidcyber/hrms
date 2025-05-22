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
            // Level 5 (Executive)
            [
                'name' => 'Chief Executive Officer',
                'level' => 5,
                'salary_min' => 15000,
                'salary_max' => 30000,
                'description' => 'Highest-ranking executive in the company'
            ],
            [
                'name' => 'Chief Operations Officer',
                'level' => 5,
                'salary_min' => 12000,
                'salary_max' => 25000,
                'description' => 'Oversees daily operations'
            ],

            // Level 4 (Management)
            [
                'name' => 'HR Manager',
                'level' => 4,
                'salary_min' => 7000,
                'salary_max' => 12000,
                'description' => 'Leads human resources department'
            ],
            [
                'name' => 'IT Manager',
                'level' => 4,
                'salary_min' => 8000,
                'salary_max' => 15000,
                'description' => 'Leads information technology department'
            ],

            // Level 3 (Senior Professional)
            [
                'name' => 'Senior Software Engineer',
                'level' => 3,
                'salary_min' => 5000,
                'salary_max' => 9000,
                'description' => 'Develops complex software solutions'
            ],
            [
                'name' => 'HR Business Partner',
                'level' => 3,
                'salary_min' => 4500,
                'salary_max' => 7500,
                'description' => 'Strategic HR support for business units'
            ],

            // Level 2 (Professional)
            [
                'name' => 'Accountant',
                'level' => 2,
                'salary_min' => 3500,
                'salary_max' => 6000,
                'description' => 'Handles financial records and reporting'
            ],
            [
                'name' => 'Marketing Specialist',
                'level' => 2,
                'salary_min' => 3000,
                'salary_max' => 5500,
                'description' => 'Executes marketing campaigns'
            ],

            // Level 1 (Entry-level/Support)
            [
                'name' => 'Customer Support Representative',
                'level' => 1,
                'salary_min' => 2000,
                'salary_max' => 3500,
                'description' => 'Provides frontline customer service'
            ],
            [
                'name' => 'Office Assistant',
                'level' => 1,
                'salary_min' => 1800,
                'salary_max' => 3000,
                'description' => 'Provides administrative support'
            ]
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
