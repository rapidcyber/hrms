<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;

class PayrollProcessing extends Component
{
    public $periodStart;
    public $periodEnd;
    public $employees;
    public $selectedEmployees = [];
    public $selectAll = false;
    public $showingDeductions = null;
    public $showDeductionModal = false;
    public $editingDeduction = false;
    public $recentPayrolls = [];
    public $confirmDelete = false;
    public $deduction = [
        'name' => '',
        'type' => 'tax',
        'calculation_type' => 'fixed',
        'default_amount' => 0,
        'description' => '',
        'is_active' => true
    ];


    public function mount()
    {
        $this->employees = collect();
        $this->periodStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->periodEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function loadEmployees()
    {
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after:periodStart'
        ]);

        $this->employees = Employee::with(['deductions' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->each(function($employee) {
                $employee->total_deductions = $employee->deductions->sum(function($deduction) use($employee) {
                    return $deduction->calculation_type === 'percentage'
                        ? $employee->base_salary * ($deduction->default_amount / 100)
                        : $deduction->default_amount;
                });
            });
    }

    public function processPayroll()
    {
        $payroll = Payroll::create([
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'status' => 'processed'
        ]);

        foreach ($this->selectedEmployees as $employeeId) {
            $employee = Employee::find($employeeId);

            $payroll->employees()->attach($employeeId, [
                'gross_salary' => $employee->base_salary,
                'total_deductions' => $employee->total_deductions,
                'net_salary' => $employee->base_salary - $employee->total_deductions
            ]);
        }

        session()->flash('message', 'Payroll processed successfully!');
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedEmployees = $this->employees->pluck('id')->toArray();
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function showDeductions($employeeId)
    {
        $this->showingDeductions = $this->showingDeductions === $employeeId ? null : $employeeId;
    }

    public function createDeduction()
    {
        $this->resetDeductionForm();
        $this->editingDeduction = false;
        $this->showDeductionModal = true;
    }

    public function editDeduction($deductionId)
    {
        $deduction = Deduction::find($deductionId);
        $this->deduction = $deduction->toArray();
        $this->editingDeduction = true;
        $this->showDeductionModal = true;
    }

    public function saveDeduction()
    {
        $this->validate([
            'deduction.name' => 'required',
            'deduction.type' => 'required',
            'deduction.calculation_type' => 'required',
            'deduction.default_amount' => 'required|numeric|min:0',
        ]);

        if ($this->deduction['calculation_type'] === 'percentage' && $this->deduction['default_amount'] > 100) {
            $this->addError('deduction.default_amount', 'Percentage cannot exceed 100%');
            return;
        }

        try {
            if ($this->editingDeduction) {
                $deduction = Deduction::find($this->deduction['id']);
                $deduction->update($this->deduction);
                $message = 'Deduction updated successfully!';
            } else {
                Deduction::create($this->deduction);
                $message = 'Deduction created successfully!';
            }

            session()->flash('message', $message);
            $this->showDeductionModal = false;
            $this->resetDeductionForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving deduction: ' . $e->getMessage());
        }
    }

    public function resetDeductionForm()
    {
        $this->deduction = [
            'name' => '',
            'type' => 'tax',
            'calculation_type' => 'fixed',
            'default_amount' => 0,
            'description' => '',
            'is_active' => true
        ];
    }

    public function render()
    {

        if($this->employees->isEmpty()) {
            $this->loadEmployees();
        }
        if(empty($this->recentPayrolls)) {
            $this->recentPayrolls = Payroll::orderBy('created_at', 'desc')->take(5)->get();
        }

        return view('livewire.payroll-processing');
    }
}
