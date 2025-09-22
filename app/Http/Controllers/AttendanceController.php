<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Export a listing of the resource.
     */
    public function export(Request $request)
    {
        ini_set('memory_limit', '256M');
        $period = [
            'start_date' => now()->firstOfMonth()->format('Y-m-d'),
            'end_date' => now()->lastOfMonth()->format('Y-m-d'),
        ];

        $params = $request->all();

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $period['start_date'] = $params['start_date'];
            $period['end_date'] = $params['end_date'];
        }


        $employees = Employee::all();

        $days = Carbon::parse($period['start_date'])->toPeriod($period['end_date'])->toArray();

        $attendances = collect();

        foreach ($employees as $employee) {

            $attendance = [
                'last_name' => $employee->last_name,
                'first_name' => $employee->first_name,
                'days' => collect($days)->map(function ($day) use ($employee) {
                    $attended = $employee->attendances()->whereDate('date', $day)->first();
                    $summary = [];
                    if($attended){
                        $summary = $this->calculateWorkedHours($attended);
                        $in = $attended->in_1 ?? $attended->in_2 ?? $attended->in_3;
                        $out = $attended->out_3 ?? $attended->out_2 ?? $attended->out_1;
                        return [
                            'in' => $in,
                            'out' => $out,
                            'undertime' => $summary['undertime'],
                            'total_hours' => $summary['total_hours'],
                        ];
                    }

                }),
                'total_undertime' => 0
            ];

            $attendance['total_undertime'] = $attendance['days']->sum('undertime');

            $attendances->push($attendance);
        }
        // dd($attendances);
        // return view('attendance.export', compact('attendances'));

        $pdf = PDF::loadView('attendance.export', compact('attendances'));
        return $pdf->stream('attendances.pdf');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    private function calculateWorkedHours($attendance)
    {
        $employee = Employee::find($attendance->employee_id);
        $checkIn = $attendance->in_1 ?? $attendance->in_2 ?? $attendance->in_3 ?? null;
        $checkOut = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1 ?? null;
        $workedHours = 0;
        $timeIn = Carbon::parse($attendance->date . ' ' . $employee->shift->time_in);
        $timeOut = Carbon::parse($attendance->date . ' ' . $employee->shift->time_out);

        // Handle overnight shifts
        if ($timeOut->isBefore($timeIn)) {
            $timeOut->addDay();
        }

        $hours = $timeOut->diffInMinutes($timeIn) / 60;

        $return = [
            'total_hours' => 0,
            'undertime' => 0
        ];

        if ($checkIn && $checkOut) {
            $checkIn = Carbon::parse($checkIn);
            $checkOut = Carbon::parse($checkOut);

            $workedHours = $checkIn->diffInMinutes($checkOut) / 60;

            // Check if late
            $lates = 0;
            if ($checkIn->isAfter($timeIn)) {
                $lates = $timeIn->diffInMinutes($checkIn) / 60;
            }

            // Check if undertime
            $underTime = 0;
            if ($checkOut->isBefore($timeOut)) {
                $underTime = $checkOut->diffInMinutes($timeOut) / 60;
            }

            $totalUnderTime = $lates + $underTime;

            $return['undertime'] = $totalUnderTime;

            if ($workedHours < 0) {
                $workedHours = 0; // Prevent negative hours
            }

            if ($workedHours > 5) {
                $workedHours -= 1; // Deduct 1 hour for present or late status
            }

            $return['total_hours'] = number_format($workedHours, 2);

        }

        return $return;
    }
}
