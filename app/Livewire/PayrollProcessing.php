<?php

namespace App\Livewire;

use App\Models\Attendance;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;

class PayrollProcessing extends Component
{
    public $periodStart;
    public $periodEnd;
    // public $employees;
    public $search = '';
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
    public $summary = [];

    public function mount()
    {
        // $this->employees = collect();
        $this->periodStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->periodEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
    }



    public function processPayroll()
    {

        $cutoffStart = Carbon::parse($this->periodStart)->format('Y-m-d H:i:s');
        $cutoffEnd = Carbon::parse($this->periodEnd)->format('Y-m-d H:i:s');

        foreach ($this->selectedEmployees as $employeeId) {

            $hours_worked =  Attendance::where('employee_id', $employeeId)
                ->whereBetween('date',[$cutoffStart, $cutoffEnd])
                ->sum('hours_worked');

            $attendances = Attendance::where('employee_id', $employeeId)
                ->whereBetween('date',[$this->periodStart, $this->periodEnd])
                ->get();

            $hours_late = 0;
            foreach ($attendances as $attendance) {
                $scheduled_in = $attendance->date . ' ' .$attendance->employee->shift->time_in;
                // Assuming 'scheduled_in' and 'in_1' are datetime fields in Attendance
                if (!empty($scheduled_in) && !empty($attendance->in_1)) {
                    $scheduledIn = Carbon::parse($scheduled_in);
                    $actualIn = Carbon::parse($attendance->in_1);
                    if ($actualIn->gt($scheduledIn)) {
                        $hours_late += $scheduledIn->diffInMinutes($actualIn) / 60;
                    }
                }
            }

            if(!empty($hours_worked)){
                $payroll = new Payroll;
                $employee = Employee::find($employeeId);

                $payroll->employee_id = $employeeId;
                $payroll->period_start = $this->periodStart;
                $payroll->period_end = $this->periodEnd;
                $payroll->gross_salary = $employee->base_salary;

                // Calculate total deductions
                $total_deductions = $employee->deductions()
                    ->where('is_active', true)
                    ->get()
                    ->sum(function($deduction) use ($employee) {
                        return $deduction->calculation_type === 'percentage'
                            ? $employee->base_salary * ($deduction->default_amount / 100)
                            : $deduction->default_amount;
                    });

                $payroll->total_deductions = $total_deductions;
                $payroll->net_salary = $employee->base_salary - $total_deductions;
                $payroll->status = 'processed';
                $payroll->save();
            }

            // $payroll->employees()->attach($employeeId, [
            //     'gross_salary' => $employee->base_salary,
            //     'total_deductions' => $employee->total_deductions,
            //     'net_salary' => $employee->base_salary - $employee->total_deductions
            // ]);
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

        $weekMap = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $cutoffStart = Carbon::parse($this->periodStart)->format('Y-m-d');
        $cutoffEnd = Carbon::parse($this->periodEnd)->format('Y-m-d');

        $employees = Employee::whereHas('attendances', function($query) use($cutoffEnd,$cutoffStart) {
            // $query->whereNotNull('in_1');
                $query->whereBetween('attendances.date', [$cutoffStart, $cutoffEnd]);
            })->where(function($q){
                $q->where('first_name', 'like', '%'. $this->search.'%')
                ->orWhere('last_name', 'like', '%'. $this->search.'%')
                ->orWhere('employee_id', 'like', '%' .$this->search.'%');
            })
            // ->where('first_name', 'like', '%'. $this->search.'%')
            //       ->orWhere('last_name', 'like', '%'. $this->search.'%')
            //       ->orWhere('employee_id', 'like', '%' .$this->search.'%')
            ->get();

        // dd($weekMap[now()->dayOfWeek()]);

        // if($this->employees->isEmpty()) {
        //     $this->loadEmployees();
        // }
        if(empty($this->recentPayrolls)) {
            $this->recentPayrolls = Payroll::orderBy('created_at', 'desc')->take(5)->get();
        }
        $payrolls = Payroll::where('status', 'processed')
            ->whereBetween('period_start', [$this->periodStart, $this->periodEnd])
            ->get();

        return view('livewire.payroll-processing', compact('employees','payrolls','weekMap'));
    }
}
