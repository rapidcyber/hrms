<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing attendance data
        Attendance::truncate();

        // Get all employees
        $employees = Employee::all();

        // Seed data for the past 30 days
        for ($daysAgo = 30; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::now()->subDays($daysAgo);

            // Skip weekends (Saturday and Sunday)
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($employees as $employee) {
                // 95% chance of attendance (5% chance of absence)
                if (rand(1, 100) <= 95) {
                    $checkIn = $this->generateCheckInTime($date, $employee->shift->id);
                    $checkOut = $this->generateCheckOutTime($checkIn);

                    // Determine status (90% present, 5% late, 5% half-day)
                    $status = 'present';
                    $checkInTime = $checkIn->format('H:i');

                    if ($checkInTime > '08:15' && rand(1, 100) <= 5) {
                        $status = 'late';
                    } elseif (rand(1, 100) <= 5) {
                        $status = 'half-day';
                        $checkOut = $checkIn->copy()->addHours(4); // Half day is 4 hours
                    }

                    // Simulate biometric data (90% from biometric, 10% manual entry)
                    $source = (rand(1, 100) <= 90) ? 'biometric' : 'manual';

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $date->toDateString(),
                        'in_1' => $checkIn->format('H:i:s'),
                        'out_1' => $checkOut->format('H:i:s'),
                        'in_2' => null, // Assuming no breaks for simplicity
                        'out_2' => null, // Assuming no breaks for simplicity
                        'in_3' => null, // Assuming no additional clock-ins
                        'out_3' => null, // Assuming no additional clock-outs
                        'hours_worked' => $checkIn->diffInHours($checkOut, false),
                        'status' => $status,
                        'source' => $source,
                        'created_at' => $checkIn,
                        'updated_at' => $checkOut ?? $checkIn,
                    ]);
                }
            }
        }

        // Generate some overtime records (20% chance per employee)
        // foreach ($employees as $employee) {
        //     if (rand(1, 100) <= 20) {
        //         $date = Carbon::now()->subDays(rand(1, 30));

        //         if (!$date->isWeekend()) {
        //             $checkIn = $this->generateCheckInTime($date);
        //             $checkOut = $checkIn->copy()->addHours(10); // 10 hour workday for overtime

        //             Attendance::create([
        //                 'employee_id' => $employee->id,
        //                 'date' => $date->toDateString(),
        //                 'in_1' => $checkIn->format('H:i:s'),
        //                 'out_1' => $checkOut->format('H:i:s'),
        //                 'in_2' => null, // Assuming no breaks for simplicity
        //                 'out_2' => null, // Assuming no breaks for simplicity
        //                 'in_3' => null, // Assuming no additional clock-ins
        //                 'out_3' => null, // Assuming no additional clock-outs
        //                 'hours_worked' => $checkIn->diffInHours($checkOut, false),
        //                 'status' => $status,
        //                 'source' => $source,
        //                 'created_at' => $checkIn,
        //                 'updated_at' => $checkOut ?? $checkIn,
        //             ]);
        //         }
        //     }
        // }
    }

    /**
     * Generate realistic check-in time (between 7:00 AM and 9:30 AM)
     */
    private function generateCheckInTime(Carbon $date, $shift_id)
    {
        $hour = rand(7, 9); // Between 7 AM and 9 AM
        $minute = ($hour === 9) ? rand(0, 30) : rand(0, 59); // If 9 AM, only up to 9:30 AM
        if($shift_id === 2){
            $hour = rand(16, 18);
            $minute = ($hour === 16) ? rand(0, 30) : rand(0, 59); // If 9 AM, only up to 9:30 AM
        }
        if($shift_id === 3){
            $hour = rand(0, 2);
            $minute = ($hour === 2) ? rand(0, 30) : rand(0, 59); // If 9 AM, only up to 9:30 AM
        }

        return $date->copy()
            ->setTime($hour, $minute)
            ->addSeconds(rand(0, 59)); // Add random seconds
    }

    /**
     * Generate realistic check-out time (between 4:00 PM and 7:00 PM)
     */
    private function generateCheckOutTime(Carbon $checkIn)
    {
        $baseHour = 16; // 4 PM
        $variation = rand(-30, 180); // -30 minutes to +3 hours variation

        return $checkIn->copy()
            ->setTime($baseHour, 0)
            ->addMinutes($variation)
            ->addSeconds(rand(0, 59)); // Add random seconds
    }
}
