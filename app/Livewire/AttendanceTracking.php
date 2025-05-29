<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\Employee;
use Livewire\WithPagination;

class AttendanceTracking extends Component
{
    use WithPagination;

    public $attendanceId, $date, $checkIn, $checkOut, $status,$periodStart,$periodEnd, $employeeId;
    public $isOpen = false;
    public $sortField = 'check_in';
    public $sortDirection = 'desc';
    public $confirmDelete = false;
    public $search = '';

    public function mount ()
    {
        $this->periodStart = now()->firstOfMonth()->format('Y-m-d');
        $this->periodEnd = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $attendances = Attendance::with('employee')
            ->whereHas('employee', function($query) {
                $query->where('first_name', 'like', '%'. $this->search.'%')
                      ->orWhere('last_name', 'like', '%'. $this->search.'%')
                      ->orWhere('employee_id', 'like', '%' .$this->search.'%');
            })
            ->where('check_in', '>=', $this->periodStart)
            ->where('check_out', '<=', $this->periodEnd)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        $employees = Employee::all();

        return view('livewire.attendance-tracking', ['attendances' => $attendances, 'employees'=> $employees]);
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

    public function create(){
        $this->isOpen = true;
        $this->resetFields();
    }

    public function edit($id){
        $this->isOpen = true;
        $this->attendanceId = $id;
        $attendance = Attendance::find($id);

        if ($attendance) {
            $this->employeeId = $attendance->employee_id;
            $this->checkIn = $attendance->check_in;
            $this->checkOut = $attendance->check_out;
            $this->status = $attendance->status;
        }
    }

    public function sort($field) {
        $this->sortField = $field;
        $this->sortDirection = $this->sortDirection == 'desc' ? 'asc' : 'desc';
    }
    // Other CRUD methods similar to EmployeeManagement...
    public function resetFields()
    {
        $this->employeeId = null;
        $this->checkIn = null;
        $this->checkOut = null;
        $this->status = null;
    }
}
