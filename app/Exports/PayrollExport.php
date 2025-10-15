<?php

namespace App\Exports;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;

class PayrollExport implements FromCollection, WithHeadings
{

    protected $parameter; // This will hold your parameter

    public function __construct($parameter) {
        $this->parameter = $parameter;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $payrolls = Payroll::whereBetween('period_start', [$this->parameter['period_start'], $this->parameter['period_end']])
            ->with(['employee'])
            ->get()
            ->map(function ($payroll) {
                $employeeId = $payroll->employee_id;
                $summary = $this->calculateOTLates($employeeId);
                $absents = $this->getAbsents($employeeId);

                return [
                    'EMPLOYEE NAME' => strtoupper($payroll->employee->last_name . ', ' . $payroll->employee->first_name),
                    'DESIGNATION' => $payroll->employee->position->name ?? 'Office Staff',
                    'BASIC SALARY' => number_format($payroll->employee->base_salary / 2, 2),
                    'OVERTIME' => number_format($payroll['overtime_pay'], 2),
                    'SUNDAY OVERTIME' => number_format($summary['sunday_overtime'], 2),
                    'LATE' => number_format($summary['late_pay'], 2),
                    'UNDERTIME' => number_format($summary['undertime_pay'], 2),
                    'CASH ADVANCE' => number_format($payroll->deductions->where('effective_date', '>=', $payroll->period_start)->sum('amount') ?? 0, 2),
                    'ABSENT' => number_format($absents * $this->calculateDailyRate($employeeId),2),
                    'TOTAL' => number_format($payroll->net_salary, 2)
                ];
            });

        $addtionalRows = collect([
            [
                'EMPLOYEE NAME' => 'KON. BADONG PLEYTO JR.',
                'DESIGNATION' => 'DISTICT OFFICE',
                'BASIC SALARY' => '50,000.00',
                'OVERTIME' => '',
                'SUNDAY OVERTIME' => '',
                'LATE' => '',
                'UNDERTIME' =>'',
                'CASH ADVANCE' => '',
                'ABSENT' => '',
                'TOTAL' => '50,000.00'
            ],
            [
                'EMPLOYEE NAME' => 'GEORGE BAUTISTA',
                'DESIGNATION' => 'DISTICT OFFICE',
                'BASIC SALARY' => '25,000.00',
                'OVERTIME' => '',
                'SUNDAY OVERTIME' => '',
                'LATE' => '',
                'UNDERTIME' =>'',
                'CASH ADVANCE' => '',
                'ABSENT' => '',
                'TOTAL' => '25,000.00'
            ],
            [
                'EMPLOYEE NAME' => 'NOEL TUAZON',
                'DESIGNATION' => 'DISTICT OFFICE',
                'BASIC SALARY' => '15,000.00',
                'OVERTIME' => '',
                'SUNDAY OVERTIME' => '',
                'LATE' => '',
                'UNDERTIME' =>'',
                'CASH ADVANCE' => '',
                'ABSENT' => '',
                'TOTAL' => '15,000.00'
            ],
            [
                'EMPLOYEE NAME' => 'VIOLETA FULGENCIO',
                'DESIGNATION' => 'DISTICT OFFICE',
                'BASIC SALARY' => '7,500.00',
                'OVERTIME' => '',
                'SUNDAY OVERTIME' => '',
                'LATE' => '',
                'UNDERTIME' =>'',
                'CASH ADVANCE' => '',
                'ABSENT' => '',
                'TOTAL' => '7,500.00'
            ]
        ]);

        $payrolls = $addtionalRows->merge($payrolls);

        return $payrolls;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            // Add your column headings here, for example:
            'EMPLOYEE NAME', 'DESIGNATION', 'BASIC SALARY', 'OVERTIME', 'SUNDAY OVERTIME',
            'LATE', 'UNDERTIME', 'CASH ADVANCE', 'ABSENT', 'TOTAL'
        ];
    }


    /**
     * Calculate Overtime and Lates for the given employee
     * @param int $employeeId
     * @return array
     */
    private function calculateOTLates($employeeId){
        $startOfMonth = Carbon::parse($this->parameter['period_start'])->startOfMonth();
        $endOfMonth = Carbon::parse($this->parameter['period_start'])->endOfMonth();

        $daysInMonth = 0;
        $period = $startOfMonth->toPeriod($endOfMonth);

        $employee = Employee::find($employeeId);

        $rest_days = array_filter(json_decode($employee->rest_days));
        $restDays = array_keys($rest_days);
        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $restDays)) {
                $daysInMonth++;
            }
        }
        // Compute daily and hourly rate
        $dailyRate = $employee->base_salary / 24;

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

        $cutoffStart = Carbon::parse($this->parameter['period_start'])->format('Y-m-d H:i:s');
        $cutoffEnd = Carbon::parse($this->parameter['period_end'])->format('Y-m-d H:i:s');

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
                    if ($actualIn->subMinutes(10)->gt($scheduledIn)) {
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

    /**
     * Get the number of absent days for the given employee
     *
     * @param int $employeeId
     * @return int
     */
    private function getAbsents($employeeId)
    {
        $cutoffStart = Carbon::parse($this->parameter['period_start'])->startOfDay();
        $cutoffEnd = Carbon::parse($this->parameter['period_end'])->endOfDay();

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
        $startOfMonth = Carbon::parse($this->parameter['period_start'])->startOfMonth();
        $endOfMonth = Carbon::parse($this->parameter['period_start'])->endOfMonth();

        $daysInMonth = 0;
        $period = $startOfMonth->toPeriod($endOfMonth);

        $rest_days = array_filter(json_decode($employee->rest_days));
        $restDays = array_keys($rest_days);
        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $restDays)) {
                $daysInMonth++;
            }
        }
        // Compute daily rate
        if ($daysInMonth === 0) {
            return 0; // Avoid division by zero
        }

        return $employee->base_salary / $daysInMonth;
    }
}


