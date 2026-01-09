<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Deduction;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Import the facade
use App\Exports\PayrollExport;
use Maatwebsite\Excel\Facades\Excel;

class PayrollProcessing extends Component
{
    public $periodStart;
    public $periodEnd;
    public $employees = [];
    public $search = '', $sortField = 'first_name', $sortDirection = 'asc';
    public $selectedEmployees = [];
    public $selectedPayrolls = [];
    public $selectAll = false;
    public $selectAllPayroll = false;
    public $showingDeductions = null;
    public $showDeductionModal = false;
    public $overtimes = [0 => [
        'employee_id' => 0,
        'hours' => 0,
        'status' => false,
    ]];
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
        'loadEmployees' => 'loadEmployees',
    ];

    public function mount()
    {
        // $this->employees = collect();
        $this->loadEmployees();
    }

    public function loadEmployees()
    {
        $this->selectedEmployees = [];
        $this->selectedPayrolls = [];

        $cutoffStart = Carbon::parse($this->periodStart)->format('Y-m-d');
        $cutoffEnd = Carbon::parse($this->periodEnd)->format('Y-m-d');

        $employees = Employee::whereHas('attendances', function($query) use($cutoffEnd,$cutoffStart) {
                // $query->whereNotNull('in_1');
                $query->whereBetween('attendances.date', [$cutoffStart, $cutoffEnd]);
            })->where(function($q){
                $q->where('first_name', 'like', '%'. $this->search.'%')
                ->orWhere('last_name', 'like', '%'. $this->search.'%')
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"])
                ->orWhere('employee_id', 'like', '%' .$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
        if($employees->isNotEmpty()){
            foreach ($employees as $employee) {
                $summary = $this->calculateOTLates($employee->id);
                $this->overtimes[$employee->id] = [
                    'employee_id' => $employee->id,
                    'hours' => number_format($summary['overtime'] ?? 0, 2),
                    'status' => false,
                ];
                $employee->overtime_hours = $this->overtimes[$employee->id]['hours'];
                $employee->overtime_status = $this->overtimes[$employee->id]['status'];
            }
        }

        $this->employees = $employees;
    }

    public function processPayroll()
    {
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
        ]);

        $cutoffStart = Carbon::parse($this->periodStart)->format('Y-m-d H:i:s');
        $cutoffEnd = Carbon::parse($this->periodEnd)->format('Y-m-d H:i:s');

        foreach ($this->selectedEmployees as $employeeId) {

            $absents = $this->getAbsents($employeeId);
            $absentPay = 0;
            if($absents > 0){
                $absentPay = $absents * $this->calculateDailyRate($employeeId);
            }


            $hours_worked =  Attendance::where('employee_id', $employeeId)
                ->whereBetween('date',[$cutoffStart, $cutoffEnd])
                ->sum('hours_worked');

            if(!empty($hours_worked)){

                $overtime = $this->overtimes[$employeeId];

                $employee = Employee::find($employeeId);
                $summary = $this->calculateOTLates($employeeId);
                $payroll = new Payroll;
                $payroll->employee_id = $employeeId;
                $payroll->period_start = $this->periodStart;
                $payroll->period_end = $this->periodEnd;

                $payroll->overtime_pay = $overtime['status'] ? $overtime['hours'] * ($this->calculateDailyRate($employeeId)/8) : 0;
                $payroll->gross_salary = ($employee->base_salary / 2) + $payroll->overtime_pay + $summary['sunday_overtime'];

                $employeeDeductions = $employee->deductions->where('effective_date', '>=',$this->periodStart)->sum('amount') ?? 0;

                // Calculate total deductions
                $payroll->total_deductions = $employeeDeductions + $summary['late_pay'] + $summary['undertime_pay'] + $absentPay;

                $payroll->net_salary = $payroll->gross_salary - $payroll->total_deductions;


                if($employee->position->level < 2){
                    $summary = $this->computePerDay($employeeId);
                    $payroll->gross_salary = $summary['base_salary'];
                    // dd($summary['base_salary'], $payroll->overtime_pay, $payroll->total_deductions);

                    $payroll->net_salary = $summary['base_salary'] + $payroll->overtime_pay + $summary['sunday_overtime'] - $payroll->total_deductions;
                }

                $payroll->status = 'processed';

                // dd(($employee->base_salary / 2), $payroll->overtime_pay, $summary['sunday_overtime'],$employee->deductions->sum('amount'), $summary['late_pay'], $summary['undertime_pay'], $absentPay);
                if($payroll->save()){
                    $deductions = $employee->deductions->where('effective_date', '>=', $this->periodStart);

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
        $payrolls = Payroll::where('period_start','>=', $this->periodStart)->get();
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
            }
        }

        $this->selectedPayrolls = [];
        $this->selectAllPayroll = false;

        $this->confirmDeleteAll = false;
    }

    public function showDeductions($employeeId)
    {
        // Compute daily and hourly rate
        $this->showingDeductions = $this->showingDeductions === $employeeId ? null : $employeeId;

        $this->summary = $this->calculateOTLates($employeeId);

    }

    public function approveOvertime($employeeId)
    {
        $this->overtimes[$employeeId]['status'] = !$this->overtimes[$employeeId]['status'];

    }

    private function calculateOvertime($hours, $employeeId)
    {
        $startOfMonth = Carbon::parse($this->periodStart)->startOfMonth();
        $endOfMonth = Carbon::parse($this->periodStart)->endOfMonth();

        $daysInMonth = 0;
        $period = $startOfMonth->toPeriod($endOfMonth);

        $employee = Employee::find($employeeId);

        $rds = array_filter(json_decode($employee->rest_days));
        $restDays = array_keys($rds);
        foreach ($period as $date) {
            // Check if $date is object
            if (is_object($date)) {
                $date = Carbon::parse($date);
            }

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
        return $hourlyRate * $hours;
    }

    private function calculateOTLates($employeeId){
        $startOfMonth = Carbon::parse($this->periodStart)->startOfMonth();
        $endOfMonth = Carbon::parse($this->periodStart)->endOfMonth();

        $daysInMonth = 0;
        $period = $startOfMonth->toPeriod($endOfMonth);

        $employee = Employee::find($employeeId);

        $rest_days = array_filter(json_decode($employee->rest_days));
        $restDays = array_keys($rest_days);
        foreach ($period as $date) {
            // Check if $date is object
            if (is_object($date)) {
                $date = Carbon::parse($date);
            }
            if (!in_array($date->dayOfWeek, $restDays)) {
                $daysInMonth++;
            }
        }
        // Compute daily and hourly rate
        $dailyRate = $this->calculateDailyRate($employeeId);

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
            'undertime' => 0,
            'undertime_pay' => 0,
            'sunday_overtime' => 0,
            'hours_worked' => $attendances->sum('hours_worked'),
            'employee_id' => $employeeId,
        ];

        // Calculate Overtime and lates
        foreach ($attendances as $attendance){

            if( in_array(Carbon::parse($attendance->date)->dayOfWeek(), $restDays)){
                if(!empty($attendance->hours_worked)){
                    $summary['overtime'] += $attendance->hours_worked;
                }
            }

            if(!empty($restDays) && Carbon::parse($attendance->date)->dayOfWeek() === 0) {
                $summary['overtime'] -= $attendance->hours_worked;
                $summary['sunday_overtime'] += $hourlyRate * $attendance->hours_worked;
            }

            // Calculate Lates if not rest days
            if(!in_array(Carbon::parse($attendance->date)->dayOfWeek(), $restDays)){
                $checkIn = $attendance->in_1 ?? $attendance->in_2 ?? $attendance->in_3 ?? null;
                $checkOut = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1 ?? null;
                $scheduled_in = $attendance->date . ' ' . $attendance->employee->shift->time_in;

                if (!empty($scheduled_in) && !empty($checkIn)) {
                    $scheduledIn = Carbon::parse($scheduled_in);
                    $scheduledOut = Carbon::parse($attendance->date . ' ' . $attendance->employee->shift->time_out);
                    $actualIn = Carbon::parse($checkIn);
                    $actualOut = Carbon::parse($checkOut);
                    if ($actualIn->subMinutes(5)->gt($scheduledIn)) {
                        $summary['lates'] += $scheduledIn->diffInMinutes($actualIn) / 60;
                        $summary['late_pay'] = $summary['lates'] * $hourlyRate;
                    }
                    // Check if actual out is later than scheduled out
                    if ($actualOut->lt($scheduledOut)) {
                        $lateHours = $actualOut->diffInMinutes($scheduledOut) / 60;
                        $summary['undertime'] += $lateHours;
                        $summary['undertime_pay'] += $lateHours * $hourlyRate;
                    }

                    if(($attendance->hours_worked - 1) > $shiftHours ){
                        // Calculate Early In and overtime
                        // Early In: If employee checks in earlier than scheduled, count as overtime
                        $overtimeHours = 0;
                        if ($actualIn->lt($scheduledIn)) {
                            $earlyInHours = $actualIn->diffInMinutes($scheduledIn) / 60;

                            if($earlyInHours > 2 ){
                                $overtimeHours = $overtimeHours + $earlyInHours;
                            }

                        }
                        if ($actualOut->gt($scheduledOut)) {
                            $lateOutHours = $scheduledOut->diffInMinutes($actualOut) / 60;
                            if($lateOutHours > 1){
                                $overtimeHours = $overtimeHours + $lateOutHours;
                            }
                        }

                        if($overtimeHours > 1){
                            $summary['overtime'] += $overtimeHours;
                            $summary['ot_pay'] += $overtimeHours * $hourlyRate;
                        }
                    }
                }
            }
        }

        return $summary;
    }

    public function createDeduction()
    {
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
        ]);

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
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
        ]);
        $this->selectedEmployees = [];
        $this->selectedPayrolls = [];
        $this->dispatch('loadEmployees');
    }

    public function saveDeduction()
    {
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
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

        $this->deduction_amount = '';
        $this->deduction_type = '';
        $this->deduction_amount = 0;

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

        if(empty($this->recentPayrolls)) {
            $this->recentPayrolls = Payroll::orderBy('created_at', 'desc')->take(5)->get();
        }
        $payrolls = Payroll::whereHas('employee',function($q){
                $q->where('first_name', 'like', '%'. $this->search.'%')
                ->orWhere('last_name', 'like', '%'. $this->search.'%')
                ->orWhere('employees.employee_id', 'like', '%' .$this->search.'%');
           })
            ->where('status', 'processed')
                ->orderBy(
                    Employee::select('first_name')
                        ->whereColumn('employees.id', 'payrolls.employee_id')
                )
            ->whereBetween('period_start', [$this->periodStart, $this->periodEnd])
            ->get();

        return view('livewire.payroll-processing', compact('payrolls'));
    }
    // Preview Payrool
    public function viewPayroll($id){
        $this->showPayrollModal = true;
        $payroll = Payroll::findOrFail($id);

        $summary = $this->calculateOTLates($payroll->employee_id);

        $payroll->lates = $summary['late_pay'];

        $payroll->absents = $this->getAbsents($payroll->employee_id, [$payroll->period_start, $payroll->period_end]) * $this->calculateDailyRate($payroll->employee_id);
        $payroll->undertime_pay = $summary['undertime_pay'];
        $payroll->sunday_overtime = $summary['sunday_overtime'];

        if($payroll->employee->position->level < 2 ){
            $summary = $this->computePerDay($payroll->employee_id);
            $payroll->gross_salary = $summary['base_salary'];
        }

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
        $payroll->absents = $this->getAbsents($payroll->employee_id, [$payroll->period_start, $payroll->period_end]) * $this->calculateDailyRate($payroll->employee_id);
        $payroll->undertime_pay = $summary['undertime_pay'];
        $payroll->sunday_overtime = $summary['sunday_overtime'];

        if ($payroll->employee->position->level < 2) {
            $summary = $this->computePerDay($payroll->employee_id);
            $payroll->gross_salary = $summary['base_salary'];
        }

        if ($payroll) {
            $pdf = Pdf::loadView('payroll.payslip', ['payroll' => $payroll],[
                'defaultFont' => 'dejavu sans', // Supports most Unicode symbols
                'isHtml5ParserEnabled' => true,
                'isUnicode' => true,
            ]);
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->setPaper('A4', 'portrait')->stream();
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

        $payrolls->each(function($payroll) {
            $summary = $this->calculateOTLates($payroll->employee_id);
            $payroll->lates = $summary['late_pay'];
            $payroll->absents = $this->getAbsents($payroll->employee_id, [$payroll->period_start, $payroll->period_end]) * $this->calculateDailyRate($payroll->employee_id);
            $payroll->undertime_pay = $summary['undertime_pay'];
            $payroll->sunday_overtime = $summary['sunday_overtime'];

            if ($payroll->employee->position->level < 2) {
                $summary = $this->computePerDay($payroll->employee_id);
                $payroll->gross_salary = $summary['base_salary'];
                $payroll->sunday_overtime = $summary['sunday_overtime'];
            }
        });

        $pdf = Pdf::loadView('payroll.payrolls', ['payrolls' => $payrolls]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'payrolls_' . $start . '_' . $end . '.pdf');

    }
    // Export Payrolls
    public function exportPayrolls()
    {
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
        ]);

        $start = $this->periodStart;
        $end = $this->periodEnd;

        if(
            !empty($start) && !empty($end)
        ){
            return Excel::download(new PayrollExport(['period_start' => $start, 'period_end' => $end]), 'payrolls_' . now()->format('Y-m-d') . '.xlsx');
        }

        session()->flash('message', 'Select a date range and load employees to begin payroll processing');

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

    private function getAbsents($employeeId, $cutoff = [])
    {
        $cutoffStart = Carbon::parse($this->periodStart)->startOfDay();
        $cutoffEnd = Carbon::parse($this->periodEnd)->endOfDay();

        if(!empty($cutoff)){
            $cutoffStart = Carbon::parse($cutoff[0])->startOfDay();
            $cutoffEnd = Carbon::parse($cutoff[1])->endOfDay();
        }

        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$cutoffStart->toDateString(), $cutoffEnd->toDateString()] )->get();

        $allDates = [];
        $currentDate = $cutoffStart->copy();
        while ($currentDate->lte($cutoffEnd)) {
            // Only add if not Saturday (6) or Sunday (0)
            if (!in_array($currentDate->dayOfWeek, [6, 0])) {
                $allDates[] = $currentDate->toDateString();
            }
            $currentDate->addDay();
        }
        $presentDates = $attendance->pluck('date')->toArray();
        $absentDays = array_diff($allDates, $presentDates);
        $absentCount = count($absentDays);
        return $absentCount;
    }

    private function calculateDailyRate($employeeId)
    {
        $employee = Employee::find($employeeId);

        return ($employee->base_salary/2) / 12;
    }

    private function computePerDay($employeeId)
    {
        $employee = Employee::find($employeeId);
        $dailyRate = $this->calculateDailyRate($employeeId);
        $absentCount = $this->getAbsents($employeeId);
        $absentPay = $absentCount * $dailyRate;

        $periodCount = Carbon::parse($this->periodStart)->toPeriod(Carbon::parse($this->periodEnd));

        $restDays = array_filter(json_decode($employee->rest_days));
        $daysInPeriod = [];

        foreach ($periodCount as $date) {
            // Check if $date is object
            if (is_object($date)) {
                $date = Carbon::parse($date);
            }
            if (!in_array($date->dayOfWeek, array_keys($restDays))) {
                $daysInPeriod[] = $date->toDateString();
            }
        }

        $otlates = $this->calculateOTLates($employeeId);

        $overtimeHours = $otlates['overtime'] ?? 0;
        $overtimePay = $otlates['ot_pay'] ?? 0;
        $latePay = $otlates['late_pay'] ?? 0;
        $undertimePay = $otlates['undertime_pay'] ?? 0;
        $sundayOvertime = $otlates['sunday_overtime'] ?? 0;

        $base_salary = $dailyRate * count($daysInPeriod);

        $net_salary = $base_salary + $sundayOvertime - $absentPay - $latePay - $undertimePay;

        return [
            'daily_rate' => $dailyRate,
            'absent_count' => $absentCount,
            'absent_pay' => $absentPay,
            'overtime_hours' => $overtimeHours,
            'overtime_pay' => $overtimePay,
            'late_pay' => $latePay,
            'undertime_pay' => $undertimePay,
            'sunday_overtime' => $sundayOvertime,
            'base_salary' => $base_salary,
            'net_salary' => $net_salary,
        ];

    }

}
