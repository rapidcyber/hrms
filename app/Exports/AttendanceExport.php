<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceExport implements FromCollection
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
        $attendances = Attendance::whereBetween('date', [$this->parameter['start_date'], $this->parameter['end_date']])
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->select(
                'attendances.employee_id',
                \DB::raw("CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"),
                'attendances.in_1',
                'attendances.out_1',
                'attendances.in_2',
                'attendances.out_2',
                'attendances.in_3',
                'attendances.out_3',
                'attendances.hours_worked'
            )
            ->get();

        return $attendances;
    }
    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'In 1',
            'Out 1',
            'In 2',
            'Out 2',
            'In 3',
            'Out 3',
            'Hours Worked'
        ];
    }
}
