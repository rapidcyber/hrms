<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Deduction;
use Livewire\WithPagination;

class PayrollProcessing extends Component
{
use WithPagination;

    public $employeeId, $periodStart, $periodEnd;
    public $grossSalary, $totalDeductions, $netSalary;
    public $isOpen = false;
    public $search = '';

    public function render()
    {
        $payrolls = Payroll::with('employee')
            ->whereHas('employee', function($query) {
                $query->where('first_name', 'like', '%'.$this->search.'%')
                      ->orWhere('last_name', 'like', '%'.$this->search.'%')
                      ->orWhere('employee_id', 'like', '%'.$this->search.'%');
            })
            ->orderBy('period_end', 'desc')
            ->paginate(10);

        return view('livewire.payroll-processing', [
            'payrolls' => $payrolls,
            'employees' => Employee::all(),
            'deductions' => Deduction::where('is_active', true)->get()
        ]);
    }

    public function calculatePayroll()
    {
        $this->validate([
            'employeeId' => 'required',
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after:periodStart',
        ]);

        $employee = Employee::find($this->employeeId);
        $attendances = Attendance::where('employee_id', $this->employeeId)
                                ->whereBetween('check_in', [$this->periodStart, $this->periodEnd])
                                ->get();

        // Calculate basic salary for the period
        $basicSalary = $employee->base_salary / 2; // Assuming semi-monthly payroll

        // Calculate overtime (simplified)
        $overtimeHours = 0;
        foreach($attendances as $attendance) {
            if ($attendance->check_out) {
                $hoursWorked = $attendance->check_out->diffInHours($attendance->check_in);
                if ($hoursWorked > 8) {
                    $overtimeHours += $hoursWorked - 8;
                }
            }
        }
        $overtimePay = $overtimeHours * ($employee->base_salary / 160); // Assuming 160 regular hours/month

        // Calculate deductions
        $deductions = Deduction::where('is_active', true)->get();
        $totalDeductions = 0;

        foreach($deductions as $deduction) {
            if ($deduction->calculation_type == 'percentage') {
                $totalDeductions += $basicSalary * ($deduction->amount / 100);
            } else {
                $totalDeductions += $deduction->amount;
            }
        }

        $this->grossSalary = $basicSalary + $overtimePay;
        $this->totalDeductions = $totalDeductions;
        $this->netSalary = $this->grossSalary - $this->totalDeductions;
    }

    public function processPayroll()
    {
        Payroll::create([
            'employee_id' => $this->employeeId,
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'gross_salary' => $this->grossSalary,
            'total_deductions' => $this->totalDeductions,
            'net_salary' => $this->netSalary,
            'status' => 'pending'
        ]);

        session()->flash('message', 'Payroll processed successfully.');
        $this->resetInputFields();
    }
}
