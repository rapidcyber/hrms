<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Deduction;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Import the facade

class PayrollProcessing extends Component
{
    public $periodStart;
    public $periodEnd;
    public $search = '', $sortField = 'first_name', $sortDirection = 'asc';
    public $selectedEmployees = [];
    public $selectedPayrolls = [];
    public $selectAll = false;
    public $selectAllPayroll = false;
    public $showingDeductions = null;
    public $showDeductionModal = false;
    public $editingDeduction = false;
    public $recentPayrolls = [];
    public $confirmDelete = false;
    public $deductionId;
    public $deduction_type = "";
    public $deduction_amount;
    public $summary = [];
    public $weekMap = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];
    public $showPayrollModal = false;
    public $viewingPayroll;
    public $confirmDeleteAll = false;

    protected $listeners = [
        'loadEmployees' => '$refresh',
    ];

    public function mount()
    {
        // $this->employees = collect();
        $this->periodStart = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
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

            if(!empty($hours_worked)){

                $employee = Employee::find($employeeId);
                $summary = $this->calculateOTLates($employeeId);
                $payroll = new Payroll;
                $payroll->employee_id = $employeeId;
                $payroll->period_start = $this->periodStart;
                $payroll->period_end = $this->periodEnd;
                $payroll->gross_salary = $employee->base_salary / 2 + $summary['ot_pay'];
                // Calculate total deductions
                $payroll->overtime_pay = $summary['ot_pay'];
                $payroll->total_deductions = $employee->deductions->sum('amount') + $summary['late_pay']+$summary['absents'];
                $payroll->net_salary = $payroll->gross_salary - $payroll->total_deductions;
                $payroll->status = 'processed';
                if($payroll->save()){
                    $deductions = $employee->deductions->where('effective_date', $this->periodEnd);
                    $payroll->deductions()->attach($deductions->pluck('id')->toArray());

                    log_activity('Payroll processed for employee ID: ' . $employee->employee_id);
                    session()->flash('message', 'Payroll processed successfully!');
                    $this->selectedEmployees = [];
                }
            }
        }
    }

    public function toggleSelectAll()
    {
        $employees = Employee::all();
        if ($this->selectAll) {
            $this->selectedEmployees = $employees->pluck('id')->toArray();
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function toggleSelectAllPayroll()
    {
        $payrolls = Payroll::where('period_start', $this->periodStart)->where('period_end', $this->periodEnd)->get();
        if ($this->selectAllPayroll) {
            $this->selectedPayrolls = $payrolls->pluck('id')->toArray();
        } else {
            $this->selectedPayrolls = [];
        }
    }

    public function deleteAllPayroll(){
        $payrolls = Payroll::whereIn('id', $this->selectedPayrolls)->get();
        log_activity('Delete selected payrolls',$payrolls->count() . ' Payroll deleted for payroll ID: ' . $payrolls->first());
        foreach($payrolls as $payroll){
            if($payroll->deductions->isNotEmpty())
                $payroll->deductions()->detach();
            if($payroll->delete()){
                session()->flash('message', 'Payrolls deleted successfully!');
                $this->selectedPayrolls = [];
            }
        }
        $this->confirmDeleteAll = false;
    }

    public function showDeductions($employeeId)
    {
        // Compute daily and hourly rate
        $this->showingDeductions = $this->showingDeductions === $employeeId ? null : $employeeId;

        $this->summary = $this->calculateOTLates($employeeId);
    }

    private function calculateOTLates($employeeId){
        $startOfMonth = Carbon::parse($this->periodStart)->startOfMonth();
        $endOfMonth = Carbon::parse($this->periodStart)->endOfMonth();

        $daysInMonth = 0;
        $period = $startOfMonth->toPeriod($endOfMonth);

        $employee = Employee::find($employeeId);

        $rds = array_filter(json_decode($employee->rest_days));
        $restDays = array_keys($rds);
        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $restDays)) {
                $daysInMonth++;
            }
        }

        $dailyRate = $employee->base_salary / $daysInMonth;

        if ($employee->shift && $employee->shift->time_in && $employee->shift->time_out) {
            $timeIn = Carbon::parse($employee->shift->time_in);
            $timeOut = Carbon::parse($employee->shift->time_out);

            // Handle overnight shifts
            if ($timeOut->lessThanOrEqualTo($timeIn)) {
                $timeOut->addDay();
            }

            $shiftHours = $timeIn->diffInHours($timeOut) - 1; // -1 for lunch break
        } else {
            $shiftHours = 8; // fallback to 8 if not set
        }

        $hourlyRate = $dailyRate / $shiftHours;

        // Compute Actual Salary base on days worked
        // $this->showingDeductions = $this->showingDeductions === $employeeId ? null : $employeeId;

        $cutoffStart = Carbon::parse($this->periodStart)->format('Y-m-d H:i:s');
        $cutoffEnd = Carbon::parse($this->periodEnd)->format('Y-m-d H:i:s');

        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$cutoffStart, $cutoffEnd])->get();

        $summary = [
            'overtime'=>0,
            'ot_pay' => 0,
            'lates' => 0,
            'late_pay' => 0,
            'absents' => 0,
        ];

        // Calculate Overtime and lates
        foreach ($attendances as $attendance){

            if( in_array(Carbon::parse($attendance->date)->dayOfWeek(), $restDays)){
                if(!empty($attendance->hours_worked)){
                    $summary['overtime'] += $attendance->hours_worked;
                }
            }

            // Calculate Lates if not rest days
            if(!in_array(Carbon::parse($attendance->date)->dayOfWeek(), $restDays)){
                $checkIn = $attendance->in_1 ?? $attendance->in_2 ?? $attendance->in_3 ?? null;
                $scheduled_in = $attendance->date . ' ' . $attendance->employee->shift->time_in;

                if (!empty($scheduled_in) && !empty($checkIn)) {
                    $scheduledIn = Carbon::parse($scheduled_in);
                    $actualIn = Carbon::parse($checkIn);
                    if ($actualIn->subMinutes(10)->gt($scheduledIn)) {
                        $summary['lates'] += $scheduledIn->diffInMinutes($actualIn) / 60;
                        $summary['late_pay'] = $summary['lates'] * $hourlyRate;
                    }

                    if(($attendance->hours_worked - 1) > $shiftHours ){
                        $summary['overtime'] += $attendance->hours_worked - $shiftHours;
                        $summary['ot_pay'] += $summary['overtime'] * $hourlyRate;
                    }
                }
                //absents
                if($attendance->status == 'absent'){
                    $summary['absents'] += $dailyRate;
                }
            }
        }

        return $summary;
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
        $this->deductionId = $deductionId;
        $this->deduction_type = $deduction->type;
        $this->deduction_amount = $deduction->amount;
        $this->editingDeduction = true;
        $this->showDeductionModal = true;

    }

    public function reloadEmployees()
    {
        $this->dispatch('loadEmployees');
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
        ]);
        $this->selectedEmployees = [];
        $this->selectedPayrolls = [];

    }

    public function saveDeduction()
    {
        $this->validate([
            'deduction_amount' => 'required',
            'deduction_type' => 'required',
        ]);

        $deduction = Deduction::find($this->deductionId) ?? new Deduction;
        $employee = Employee::find($this->showingDeductions);

        $deduction->employee_id = $employee->id;
        $deduction->type = $this->deduction_type ?? 'cash-advance';
        $deduction->amount = $this->deduction_amount ?? 0;
        $deduction->updated_by = auth()->id();
        $deduction->effective_date = $this->periodEnd;

        if(!$this->editingDeduction){
            $deduction->created_by = auth()->id();
        }

        if($deduction->save()){
            $this->showDeductionModal = false;
            $this->resetDeductionForm();
            log_activity('Deduction saved for employee ID: ' . $employee->employee_id);
            session()->flash('message', 'Deduction created successfully!');
        }
    }

    public function resetDeductionForm()
    {
        $this->deduction = [
            'deduction_amount' => '',
            'deduction_type' => '',
            'amount' => 0,
        ];
    }

    public function deleteDeduction($id){
        $deduction = Deduction::find($id);

        if ($deduction) {

            log_activity('Deduction deleted for deduction ID: ' . $deduction->id);
            $deduction->delete();
            session()->flash('message', 'Deduction deleted successfully!');
            $this->confirmDelete = false;
            $this->showDeductionModal = false;
        } else {
            session()->flash('error', 'Deduction not found.');
        }

    }

    public function render()
    {

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
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        // dd($weekMap[now()->dayOfWeek()]);

        // if($this->employees->isEmpty()) {
        //     $this->loadEmployees();
        // }
        if(empty($this->recentPayrolls)) {
            $this->recentPayrolls = Payroll::orderBy('created_at', 'desc')->take(5)->get();
        }
        $payrolls = Payroll::whereHas('employee',function($q){
                $q->where('first_name', 'like', '%'. $this->search.'%')
                ->orWhere('last_name', 'like', '%'. $this->search.'%')
                ->orWhere('employees.employee_id', 'like', '%' .$this->search.'%');
           })
            ->where('status', 'processed')
            ->whereBetween('period_start', [$this->periodStart, $this->periodEnd])
            ->get();

        return view('livewire.payroll-processing', compact('employees','payrolls'));
    }
    // Preview Payrool
    public function viewPayroll($id){
        $this->showPayrollModal = true;
        $payroll = Payroll::findOrFail($id);

        $summary = $this->calculateOTLates($payroll->employee_id);

        $payroll->lates = $summary['late_pay'];
        $payroll->absents = $summary['absents'];

        $this->viewingPayroll = $payroll;

    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    // Download Payroll

    public function downloadPayroll($id){
        $payroll = Payroll::find($id);
        $summary = $this->calculateOTLates($payroll->employee_id);

        $payroll->lates = $summary['late_pay'];
        $payroll->absents = $summary['absents'];

        if ($payroll) {
            $pdf = Pdf::loadView('payroll.payslip', ['payroll' => $payroll],[
                'defaultFont' => 'dejavu sans', // Supports most Unicode symbols
                'isHtml5ParserEnabled' => true,
                'isUnicode' => true,
            ]);
            return response()->streamDownload(function () use ($pdf) {
                $pdf->setPaper('A4', 'portrait')->stream();
            }, 'payroll_' . $payroll->id . '.pdf');
        } else {
            session()->flash('error', 'Payroll not found.');
            return redirect()->back();
        }
    }

    // Print
    public function printPayrolls(){
        $start = $this->periodStart;
        $end = $this->periodEnd;

        $payrolls = Payroll::where('period_start', $start)->where('period_end', $end)->get();

        $pdf = Pdf::loadView('payroll.payrolls', ['payrolls' => $payrolls]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'payrolls_' . $start . '_' . $end . '.pdf');

    }

    public function deletePayroll($id){
        $payroll = Payroll::find($id);

        if ($payroll) {
            $payroll->delete();
            log_activity('Payroll deleted for payroll ID: ' . $id);
            session()->flash('message', 'Payroll deleted successfully!');
        } else {
            session()->flash('error', 'Payroll not found.');
        }
    }
}
