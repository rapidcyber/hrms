<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\Employee;
use Livewire\WithPagination;

class AttendanceTracking extends Component
{
use WithPagination;

    public $employeeId, $date, $checkIn, $checkOut, $status;
    public $isOpen = false;
    public $search = '';

    public function render()
    {
        $attendances = Attendance::with('employee')
            ->whereHas('employee', function($query) {
                $query->where('first_name', 'like', '%'.$this->search.'%')
                      ->orWhere('last_name', 'like', '%'.$this->search.'%')
                      ->orWhere('employee_id', 'like', '%'.$this->search.'%');
            })
            ->orderBy('check_in', 'desc')
            ->paginate(10);

        return view('livewire.attendance-tracking', ['attendances' => $attendances]);
    }

    public function syncBiometricData()
    {
        // This would integrate with ZKTeco MultiBio800 API
        // For demo purposes, we'll simulate data

        $employees = Employee::inRandomOrder()->limit(5)->get();

        foreach($employees as $employee) {
            Attendance::create([
                'employee_id' => $employee->id,
                'check_in' => now()->subHours(9),
                'check_out' => now(),
                'status' => 'present',
                'source' => 'biometric'
            ]);
        }

        session()->flash('message', 'Biometric data synced successfully.');
    }

    // Other CRUD methods similar to EmployeeManagement...
}
