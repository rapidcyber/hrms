<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Handles recruitment, employee relations, and company policies, finances, payroll, and accounting'
            ],
            [
                'name' => 'Utilities',
                'code' => 'UD',
                'description' => 'Manages company sanitation, food and maintenance'
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Responsible for technology infrastructure and systems'
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Handles brand management and promotional activities, Brand guidlines and multi-media'
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Manages daily business operations and logistics'
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
