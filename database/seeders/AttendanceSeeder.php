<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use App\Models\Employee;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $startDate = now()->subDays(7);
        $endDate = now();

        $shifts = [
            [
                'name' => 'Morning Shift',
                'time_in' => '08:00:00',
                'time_out' => '17:00:00',
                'rest_days' => [0, 6],
            ],
            [
                'name' => 'Day Shift',
                'time_in' => '06:00:00',
                'time_out' => '18:00:00',
                'rest_days' => [0, 6],
            ],
            [
                'name' => 'Night Shift',
                'time_in' => '18:00:00',
                'time_out' => '06:00:00',
                'rest_days' => [0, 6],
            ],
        ];

        foreach ($employees as $employee) {
            $date = $startDate->copy();
            while ($date <= $endDate) {
                $weekday = $date->dayOfWeek;

                $shift = collect($shifts)->random();

                if (in_array($weekday, $shift['rest_days'])) {
                    $date->addDay();
                    continue;
                }

                $isNightShift = $shift['time_out'] < $shift['time_in'];

                $shiftIn = Carbon::parse($date->toDateString() . ' ' . $shift['time_in']);
                $shiftOut = $isNightShift
                    ? Carbon::parse($date->copy()->addDay()->toDateString() . ' ' . $shift['time_out'])
                    : Carbon::parse($date->toDateString() . ' ' . $shift['time_out']);

                $graceMinutes = rand(0, 15);
                $checkIn = $shiftIn->copy()->addMinutes($graceMinutes);

                $lateThreshold = $shiftIn->copy()->addMinutes(10);
                $status = $checkIn->gt($lateThreshold) ? 'late' : 'present';
                $remarks = $checkIn->gt($lateThreshold) ? 'Late arrival' : null;

                $in1 = $checkIn;
                $out1 = null;
                $in2 = null;
                $out2 = $shiftOut;

                // Calculate hours worked
                $durationInMinutes = $in1->diffInMinutes($out2);
                $hoursWorked = $durationInMinutes / 60;

                // Subtract 1 hour break if > 4 hrs
                if ($hoursWorked > 4) {
                    $hoursWorked -= 1;
                }

                $hoursWorked = round($hoursWorked, 2);

                Attendance::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date->toDateString()],
                    [
                        'in_1' => $in1,
                        'out_1' => $out1,
                        'in_2' => $in2,
                        'out_2' => $out2,
                        'in_3' => null,
                        'out_3' => null,
                        'hours_worked' => $hoursWorked,
                        'status' => $status,
                        'remarks' => $remarks,
                        'source' => 'seeder',
                    ]
                );

                $date->addDay();
            }
        }
    }
}
