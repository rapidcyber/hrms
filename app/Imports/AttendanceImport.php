<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

use function Termwind\parse;

class AttendanceImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $shifts = Shift::all();
        $employee = null;
        // dd($rows->skip(20));
        foreach ($rows->skip(20) as $row) {

            $date = $row[2];
            $in_1 = !empty($row[3]) ? Carbon::parse($date . ' ' . $row[3]) : null;
            $out_1 = !empty($row[4]) ? Carbon::parse($date . ' ' . $row[4]) : null;
            $in_2 = !empty($row[5]) ? Carbon::parse($date . ' ' . $row[5]) : null;
            $out_2 = !empty($row[6]) ? Carbon::parse($date . ' ' . $row[6]) : null;
            $in_3 = !empty($row[7]) ? Carbon::parse($date . ' ' . $row[7]) : null;
            $out_3 = $row[8] && Carbon::canBeCreatedFromFormat('H:i:s', $row[8]) ? Carbon::parse($date . ' ' . $row[8]) : null;

            $status = 'present';

            $checkIn = $in_1 ?? $in_2 ?? $in_3 ?? null;
            $checkOut = $out_3 ?? $out_2 ?? $out_1 ?? null;

            $shiftId = 1;
            $shiftHrs = 12;

            if($checkIn && Carbon::parse($checkIn)->lt(Carbon::parse($date .' 6:30:00')) ) {
                $shiftId = 2;
                $shiftHrs = 10;
            }

            if($checkIn && Carbon::parse($checkIn)->gt(Carbon::parse('16:30:00')) ) {
                $shiftId = 3;
            }

            $shift = $shifts->where('id', $shiftId)->first();

            if(Carbon::parse($checkIn)->subMinutes(10) > Carbon::parse($date .' '. $shift->time_in)){
                $status = 'late';
            }

            if(Carbon::parse($checkIn)->subHours(4) > Carbon::parse($date .' '. $shift->time_in)){
                $status = 'half-day';
            }

            if(is_null($checkIn)){
                $status = 'absent';
            }
            $restDays = [
                0=> 'Sunday',
                1 => null,
                2 => null,
                3 => null,
                4 => null,
                5 => null,
                6 => 'Saturday'
            ];
            if(in_array(Carbon::parse($date)->dayOfWeek(), array_keys(array_filter($restDays)))){
                $status = 'rest-day';
            }

            if($shiftId === 3){
                $restDays = [
                    0 => null,
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => null,
                    6 => 'Saturday'
                ];
            }

            if(!empty($row[1])){

                if(Employee::where('employee_id', $row[1])->exists()){
                    $employee = Employee::where('employee_id', $row[1])->first();
                } else {
                    $employee = new Employee;
                    $employeeName = explode(',',trim($row[0]));
                    $employee->employee_id = $row[1];
                    $employee->last_name = $employeeName[0];
                    $employee->first_name = $employeeName[1] ?? '';
                    $employee->email = $row[1] .'changethis@email.com';
                    $employee->phone = '+639000000000';
                    $employee->date_of_birth = '2000-01-01';
                    $employee->hire_date = now()->format('Y-m-d');
                    $employee->base_salary = 0.00;
                    $employee->department_id = 5;
                    $employee->position_id = 6;
                    $employee->shift_id = $shiftId;
                    $employee->rest_days = json_encode($restDays);
                    $employee->save();
                }
            }

            if(!empty($date)){
                $date = Carbon::parse($date)->format('Y-m-d');
                $attendance = Attendance::where('employee_id', $employee->id)->first() ?? new Attendance;

                if($attendance->date != $date){
                    $attendance = new Attendance;
                }




                $hrs = is_numeric($row[9]) ? round($row[9], 2) : 0;

                if($hrs > ($shiftHrs/2)){
                    $hrs = $hrs - 1;
                }

                $attendance->employee_id = $employee->id;
                $attendance->date = $date;
                $attendance->in_1 = $in_1;
                $attendance->out_1 = $out_1;
                $attendance->in_2 = $in_2;
                $attendance->out_2 = $out_2;
                $attendance->in_3 = $in_3;
                $attendance->out_3 = $out_3;
                $attendance->hours_worked = $hrs;
                $attendance->status = $status;
                $attendance->source = 'imported';

                $attendance->save();
            }

        }
    }
}
