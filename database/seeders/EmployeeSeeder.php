<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create departments if they don't exist
        $departments = [
            ['name' => 'Human Resources'],
            ['name' => 'Finance'],
            ['name' => 'Information Technology'],
            ['name' => 'Marketing'],
            ['name' => 'Operations'],
            ['name' => 'Sales'],
            ['name' => 'Customer Support'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate($department);
        }

        // Create positions if they don't exist
        $positions = [
            ['name' => 'Manager', 'level' => 5],
            ['name' => 'Senior Developer', 'level' => 4],
            ['name' => 'Developer', 'level' => 3],
            ['name' => 'Junior Developer', 'level' => 2],
            ['name' => 'HR Specialist', 'level' => 3],
            ['name' => 'Accountant', 'level' => 3],
            ['name' => 'Marketing Specialist', 'level' => 3],
            ['name' => 'Sales Representative', 'level' => 2],
            ['name' => 'Customer Support Agent', 'level' => 1],
            ['name' => 'Operations Coordinator', 'level' => 2],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate($position);
        }

        // Get all departments and positions
        $departments = Department::all();
        $positions = Position::all();

        // Initialize Faker
        $faker = Faker::create();

        // Generate 50 employees
        for ($i = 1; $i <= 50; $i++) {
            $hireDate = Carbon::now()->subYears(rand(1, 5))->subMonths(rand(0, 11))->subDays(rand(0, 30));
            $birthDate = Carbon::now()->subYears(rand(22, 55))->subMonths(rand(0, 11))->subDays(rand(0, 30));

            // Determine salary based on position level
            $position = $positions->random();
            $baseSalary = $this->generateBaseSalary($position->level);

            // Generate biometric ID (simulating ZKTeco device format)
            $biometricId = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            Employee::create([
                'employee_id' => 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => 'employee' . $i . '@hrmpro.com',
                'phone' => $faker->phoneNumber,
                'date_of_birth' => $birthDate,
                'hire_date' => $hireDate,
                'base_salary' => $baseSalary,
                'department_id' => $departments->random()->id,
                'position_id' => $position->id,
                'shift_id' => rand(1, 3), // Assuming you have 3 shifts
                // 'biometric_id' => $biometricId,
            ]);
        }

        // Create some admin/test users
        $this->createTestEmployees();
    }

    /**
     * Generate base salary based on position level
     */
    private function generateBaseSalary($level)
    {
        $baseSalaries = [
            1 => [1800, 2500],   // Level 1 positions
            2 => [2500, 3500],   // Level 2 positions
            3 => [3500, 5000],   // Level 3 positions
            4 => [5000, 7500],   // Level 4 positions
            5 => [7500, 12000],  // Level 5 positions
        ];

        $range = $baseSalaries[$level];
        return rand($range[0] * 100, $range[1] * 100) / 100; // Random salary within range
    }

    /**
     * Create specific test employees
     */
    private function createTestEmployees()
    {
        $departments = Department::all();
        $positions = Position::all();

        // HR Manager
        Employee::create([
            'employee_id' => 'EMP0101',
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.johnson@hrmpro.com',
            'phone' => '+1234567890',
            'date_of_birth' => Carbon::now()->subYears(35),
            'hire_date' => Carbon::now()->subYears(3),
            'base_salary' => 8500.00,
            'department_id' => $departments->where('name', 'Human Resources')->first()->id,
            'position_id' => $positions->where('name', 'Manager')->first()->id,
            'biometric_id' => '101',
        ]);

        // IT Manager
        Employee::create([
            'employee_id' => 'EMP0102',
            'first_name' => 'Michael',
            'last_name' => 'Chen',
            'email' => 'michael.chen@hrmpro.com',
            'phone' => '+1234567891',
            'date_of_birth' => Carbon::now()->subYears(40),
            'hire_date' => Carbon::now()->subYears(4),
            'base_salary' => 9500.00,
            'department_id' => $departments->where('name', 'Information Technology')->first()->id,
            'position_id' => $positions->where('name', 'Manager')->first()->id,
            'biometric_id' => '102',
        ]);

        // Finance Specialist
        Employee::create([
            'employee_id' => 'EMP0103',
            'first_name' => 'Emily',
            'last_name' => 'Rodriguez',
            'email' => 'emily.rodriguez@hrmpro.com',
            'phone' => '+1234567892',
            'date_of_birth' => Carbon::now()->subYears(28),
            'hire_date' => Carbon::now()->subYears(2),
            'base_salary' => 4500.00,
            'department_id' => $departments->where('name', 'Finance')->first()->id,
            'position_id' => $positions->where('name', 'Accountant')->first()->id,
            'biometric_id' => '103',
        ]);
    }
}
