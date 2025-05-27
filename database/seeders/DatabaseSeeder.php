<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@hrms-scm.com',
        ]);

        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            DeductionSeeder::class,
            ShiftSeeder::class,
            EmployeeSeeder::class,
            AttendanceSeeder::class,
        ]);

            // Now assign department managers
        $this->assignDepartmentManagers();
    }

    protected function assignDepartmentManagers()
    {
        $departments = Department::all();

        foreach ($departments as $department) {
            // Find an employee in this department to be manager
            $manager = Employee::where('department_id', $department->id)
                            ->inRandomOrder()
                            ->first();

            if ($manager) {
                $department->update(['manager_id' => $manager->id]);
            }
        }
    }
}
