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
                'description' => 'Handles recruitment, employee relations, and company policies'
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Manages company finances, payroll, and accounting'
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Responsible for technology infrastructure and systems'
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Handles brand management and promotional activities'
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Manages daily business operations and logistics'
            ],
            [
                'name' => 'Sales',
                'code' => 'SALES',
                'description' => 'Responsible for revenue generation and client acquisition'
            ],
            [
                'name' => 'Customer Support',
                'code' => 'CS',
                'description' => 'Provides assistance to customers and resolves issues'
            ],
            [
                'name' => 'Research & Development',
                'code' => 'RND',
                'description' => 'Focuses on product innovation and improvement'
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
