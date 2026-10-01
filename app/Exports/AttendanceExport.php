<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection,WithHeadings
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
        $startDate = Carbon::parse($this->parameter['start_date']);
        $endDate = Carbon::parse($this->parameter['end_date']);
        $period = $startDate->toPeriod($endDate);
        $employees = Employee::all();

        $newAttandances = collect();
        foreach ($period as $date) {
            foreach ($employees as $employee) {
                $attendancesDate = $employee->attendances->filter(function ($attendance) use ($date, $employee) {
                    return Carbon::parse($attendance->date)->isSameDay($date) && $attendance->employee_id == $employee->id;
                });
                if ($attendancesDate->isEmpty()) {
                    // If no attendance for this employee on this date, create a new attendance record
                    $newAttendance = [
                        'employee_id' => $employee->employee_id,
                        'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                        'date' => $date->format('m-d-Y'),
                        'in_1' => null,
                        'out_1' => null,
                        'in_2' => null,
                        'out_2' => null,
                        'in_3' => null,
                        'out_3' => null,
                        'hours_worked' => 0,
                        'status' => 'absent',
                        'remarks' => null,
                    ];
                    $newAttandances->push($newAttendance);
                } else {
                    $newAttandances = $newAttandances->merge($attendancesDate->map(function ($attendance) {
                        return [
                            'employee_id' => $attendance->employee->employee_id,
                            'employee_name' => $attendance->employee->first_name . ' ' . $attendance->employee->last_name,
                            'date' => Carbon::parse($attendance->date)->format('m-d-Y'),
                            'in_1' => $attendance->in_1 ? Carbon::parse($attendance->in_1)->format('H:i:s') : null,
                            'out_1' => $attendance->out_1 ? Carbon::parse($attendance->out_1)->format('H:i:s') : null,
                            'in_2' => $attendance->in_2 ? Carbon::parse($attendance->in_2)->format('H:i:s') : null,
                            'out_2' => $attendance->out_2 ? Carbon::parse($attendance->out_2)->format('H:i:s') : null,
                            'in_3' => $attendance->in_3 ? Carbon::parse($attendance->in_3)->format('H:i:s') : null,
                            'out_3' => $attendance->out_3 ? Carbon::parse($attendance->out_3)->format('H:i:s') : null,
                            'hours_worked' => $attendance->hours_worked,
                            'status' => $attendance->status,
                            'remarks' => $attendance->remarks,
                        ];
                    }));
                }
            }
        }

        return $newAttandances->sortBy('date')->sortBy('employee_id');
    }
    /**
     * Define the headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Date',
            'In 1',
            'Out 1',
            'In 2',
            'Out 2',
            'In 3',
            'Out 3',
            'Hours Worked',
            'Status',
            'Remarks'
        ];
    }
}
