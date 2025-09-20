<?php

namespace App\Exports;

use App\Models\Attendance;
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
        $attendances = Attendance::whereBetween('date', [$this->parameter['start_date'], $this->parameter['end_date']])
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->select(
                'attendances.employee_id',
                \DB::raw("CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"),
                'attendances.date',
                \DB::raw("TIME_FORMAT(attendances.in_1, '%H:%i:%s') as in_1"),
                \DB::raw("TIME_FORMAT(attendances.out_1, '%H:%i:%s') as out_1"),
                \DB::raw("TIME_FORMAT(attendances.in_2, '%H:%i:%s') as in_2"),
                \DB::raw("TIME_FORMAT(attendances.out_2, '%H:%i:%s') as out_2"),
                \DB::raw("TIME_FORMAT(attendances.in_3, '%H:%i:%s') as in_3"),
                \DB::raw("TIME_FORMAT(attendances.out_3, '%H:%i:%s') as out_3"),
                'attendances.hours_worked'
            )
            ->get();

        return $attendances;
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
            'Hours Worked'
        ];
    }
}
