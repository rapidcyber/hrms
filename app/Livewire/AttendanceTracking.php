<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\Employee;
use Livewire\WithPagination;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AttendanceImport;
use App\Exports\AttendanceExport;
use Livewire\WithFileUploads;
use Laradevsbd\Zkteco\Http\Library\ZktecoLib;
use Illuminate\Support\Facades\DB;

class AttendanceTracking extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $attendanceId, $date, $checkIn, $checkOut, $status,$remarks,$periodStart,
        $periodEnd, $employeeId,$file;
    public $isOpen = false;
    public $sortField = 'date';
    public $sortDirection = [
        'employees.first_name' => 'asc',
        'date'=>'desc'
    ];
    public $confirmDelete = false;
    public $search = '';

    public $weekMap = [
        'Sun',
        'Mon',
        'Tue',
        'Wed',
        'Thu',
        'Fri',
        'Sat',
    ];

    protected $listeners = ['fileSelected'];

    public function render()
    {
        if (empty($this->periodStart) || empty($this->periodEnd)) {
            return view('livewire.attendance-tracking', ['attendances' => collect(), 'employees'=> Employee::latest()->get()]);
        }

        $employees = Employee::latest()->get();

        $period = [];
        if ($this->periodStart && $this->periodEnd) {
            $start = Carbon::parse($this->periodStart);
            $end = Carbon::parse($this->periodEnd);
            $period = $start->toPeriod($end);

            $newAttandances = collect();
            foreach ($period as $date) {
                foreach ($employees as $employee) {
                    // Check if attendance exists for this employee on this date

                    $attendancesDate = $employee->attendances->filter(function ($attendance) use ($date, $employee) {
                        return Carbon::parse($attendance->date)->isSameDay($date) && $attendance->employee_id == $employee->id;
                    });
                    if ($attendancesDate->isEmpty()) {
                        // If no attendance for this employee on this date, create a new attendance record
                        $newAttendance = new Attendance();
                        $newAttendance->date = $date->format('Y-m-d');
                        $newAttendance->employee_id = $employee->id;
                        $newAttendance->status = 'absent'; // Default status
                        $newAttendance->source = 'manual'; // Default source
                        $newAttendance->hours_worked = 0; // Default hours worked
                        $newAttandances->push($newAttendance);
                    } else {
                        $newAttandances = $newAttandances->merge($attendancesDate);
                    }
                }
            }
            // Filter by employee first_name if search is provided
            if (!empty($this->search)) {
                $newAttandances = $newAttandances->filter(function ($attendance) use ($employees) {
                    $employee = $employees->firstWhere('id', $attendance->employee_id);
                    $fullName = $employee ? strtolower($employee->first_name . ' ' . $employee->last_name) : '';
                    return $fullName && stripos($fullName, strtolower($this->search)) !== false;
                });
            }
            // Sort by employee first_name and date according to sortDirection
            $newAttandances = $newAttandances->sort(function ($a, $b) use ($employees) {
                $employeeA = $employees->firstWhere('id', $a->employee_id);
                $employeeB = $employees->firstWhere('id', $b->employee_id);
                $firstNameA = $employeeA ? strtolower($employeeA->first_name) : '';
                $firstNameB = $employeeB ? strtolower($employeeB->first_name) : '';
                $dateA = $a->date ?? '';
                $dateB = $b->date ?? '';

                // Sort by first_name
                $firstNameDirection = $this->sortDirection['employees.first_name'] === 'desc' ? -1 : 1;
                if ($firstNameA !== $firstNameB) {
                    return $firstNameDirection * strcmp($firstNameA, $firstNameB);
                }

                // Sort by date
                $dateDirection = $this->sortDirection['date'] === 'desc' ? -1 : 1;
                return $dateDirection * strcmp($dateA, $dateB);
            })->values();

            // dd($newAttandances->count());
            $perPage = 10;
            $paginatedAttendances = $newAttandances->paginate($perPage);
        }

        // Convert the attendance collection to a Laravel Collection for easier manipulation

        return view('livewire.attendance-tracking', ['attendances' => $paginatedAttendances, 'employees'=> $employees]);
    }

    public function syncBiometricData()
    {

        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
        ]);

        ini_set('max_execution_time', 600); // 600 seconds
        $zk = new ZktecoLib('192.168.1.142', 4370); // Default port: 4370
        $zk->connect();
        $attendances = $zk->getAttendance();

        // Convert array to a Laravel Collection for easier manipulation

        // Generate sample attendance data for the last 2 months
        // $attendances = [];
        // $employeeIds = [12, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43];
        // $bioIdBase = 65000000000;
        // $startDate = Carbon::now()->subMonths(1)->startOfMonth();
        // $endDate = Carbon::now()->endOfMonth();

        // foreach ($employeeIds as $i => $employeeId) {
        //     $bioId = $bioIdBase + ($i * 300000000);
        //     $date = $startDate->copy();
        //     while ($date->lte($endDate)) {
        //         // Only weekdays (Mon-Fri)
        //         if ($date->isWeekday()) {
        //             // Simulate check-in between 7:30-9:00 and check-out between 16:30-18:00
        //             $checkIn = $date->copy()->setTime(rand(7, 8), rand(30, 59), rand(0, 59))->format('Y-m-d H:i:s');
        //             $checkOut = $date->copy()->setTime(rand(16, 17), rand(30, 59), rand(0, 59))->format('Y-m-d H:i:s');
        //             $attendances[] = [$bioId, $employeeId, 1, $checkIn];
        //             $attendances[] = [$bioId, $employeeId, 1, $checkOut];
        //         }
        //         $date->addDay();
        //     }
        // }

        $punches = collect($attendances)->map(function ($record) {
            return [
                'bio_id' => $record[0],
                'employee_id' => $record[1],
                'in_out_mode' => $record[2],
                'timestamp' => Carbon::parse($record[3]),
            ];
        });

        try {
            DB::transaction(function () use ($punches) {
                foreach ($punches->whereBetween('timestamp', [$this->periodStart, $this->periodEnd]) as $punch) {
                    $attendance = Attendance::where('employee_id', $punch['employee_id'])
                        ->whereDate('date', $punch['timestamp']->toDateString())
                        ->first();

                    $employee = Employee::find($punch['employee_id']);
                    if (!$employee) {
                        continue; // Skip if employee not found
                    }
                    // If attendance record does not exist, create a new one
                    if (!$attendance) {
                        $attendance = new Attendance();
                        $attendance->employee_id = $punch['employee_id'];
                        $attendance->date = $punch['timestamp']->toDateString();
                    }

                    // Set in/out pairs based on in_out_mode
                    $time = $punch['timestamp'];

                    // Set in/out pairs based on shift
                    // Assume shift has start_time and end_time (e.g., '08:00:00', '17:00:00')
                    $shift = $employee->shift;
                    $shiftStart = $shift && $shift->start_time ? Carbon::parse($attendance->date . ' ' . $shift->start_time) : Carbon::parse($attendance->date . ' 08:00:00');
                    $shiftEnd = $shift && $shift->end_time ? Carbon::parse($attendance->date . ' ' . $shift->end_time) : Carbon::parse($attendance->date . ' 17:00:00');

                    // Collect all punches for this employee and date
                    $allPunches = $punches->filter(function ($p) use ($punch) {
                        return $p['employee_id'] === $punch['employee_id'] && $p['timestamp']->toDateString() === $punch['timestamp']->toDateString();
                    })->sortBy('timestamp')->values();
                    // Assign in/out pairs
                    if ($allPunches->count() > 0) {
                        // First punch is check-in
                        $attendance->in_1 = $allPunches[0]['timestamp'];
                        // If more than 1 punch, last punch is check-out
                        if ($allPunches->count() > 1) {
                            $attendance->out_1 = $allPunches[$allPunches->count() - 1]['timestamp'];
                        }
                        // If more than 2 punches, second punch is out_2 (break out), third punch is in_2 (break in)
                        if ($allPunches->count() > 2) {
                            $attendance->in_2 = $allPunches[1]['timestamp'];
                            $attendance->out_2 = $allPunches[2]['timestamp'];
                        }
                        // If more than 3 punches, last punch is out_3 (final checkout)
                        if ($allPunches->count() > 3) {
                            $attendance->out_3 = $allPunches[$allPunches->count() - 1]['timestamp'];
                        }
                    }

                    $status = 'present';
                    $checkIn = $attendance->in_1 ?? $attendance->in_2 ?? $attendance->in_3;
                    $checkOut = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1;
                    $date = $attendance->date;
                    if ($checkIn && Carbon::parse($checkIn)->subMinutes(10)->gt(Carbon::parse($date . ' ' . $shift->time_in))) {
                        $status = 'late';
                    }

                    if (is_null($checkIn)) {
                        $status = 'absent';
                    }
                    $restDays = $employee->rest_days ?? null;
                    if($employee->rest_days){
                        $restDays = json_decode($employee->rest_days, true);
                    }
                    if (in_array(Carbon::parse($attendance->date)->dayOfWeek(), array_keys(array_filter($restDays)))) {
                        $status = 'rest-day';
                    }

                    $attendance->status = $status;
                    $attendance->source = 'biometric';
                    // Set hours based on shift
                    $shift = $employee->shift;
                    $shiftId = 1;
                    if ($shift) {
                        $shiftId = $shift->id;
                    }
                    $attendance->hours_worked = 0;
                    if ($checkIn && $checkOut) {
                        $attendance->hours_worked = Carbon::parse($checkIn)->diffInMinutes(Carbon::parse($checkOut)) / 60;
                    }

                    if ($checkIn && $checkOut && $attendance->hours_worked > 0 && $attendance->hours_worked < 5) {
                        $status = 'half-day';
                    }

                    if ($status === 'present' || $status === 'late') {
                        $attendance->hours_worked = $attendance->hours_worked - 1; // Ensure hours_worked is set
                    }

                    if ($attendance->hours_worked < 0) {
                        $attendance->hours_worked = 0; // Prevent negative hours
                    }
                    // Save the attendance record
                    $attendance->save();
                }
            });
            log_activity('attendance_synced', 'Biometric data synced via ZKTeco import', null, []);
            session()->flash('message', 'Biometric data synced successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

    public function create(){
        $this->isOpen = true;
        $this->resetFields();
    }

    public function newEmployeeAttendance($employeeId){
        $this->isOpen = true;

    }

    public function edit($id = null, $employeeId){

        $this->isOpen = true;
        $this->resetFields();
        $this->attendanceId = null;
        $this->employeeId = $employeeId;
        $this->status = 'present';
        $this->remarks = '';

        if ($id) {
            $this->attendanceId = $id;
            $attendance = Attendance::find($id);
            if ($attendance) {
                $this->employeeId = $attendance->employee_id;
                $this->date = $attendance->date;
                $this->checkIn = $attendance->in_1 ?? $attendance->in_2 ?? $attendance->in_3;
                $this->checkOut = $attendance->out_3 ?? $attendance->out_2 ?? $attendance->out_1;
                $this->status = $attendance->status;
                $this->remarks = $attendance->remarks;
            }
        }
    }

    public function updateDate(){
        $this->date = Carbon::parse($this->checkIn)->format('Y-m-d');
    }

    public function fileSelected(){

        $this->import();

    }

    public function import(){

        $this->validate([
            'file' => 'required|file',
        ]);

        $import = Excel::import(new AttendanceImport, $this->file);


        session()->flash('message', 'Attendance data imported successfully.');
    }

    public function export()
    {
        $this->validate([
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
        ]);

        return Excel::download(new AttendanceExport(['start_date' => $this->periodStart, 'end_date' => $this->periodEnd]), 'attendance_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function store()
    {

        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'date' => 'required|date|unique:attendances,date,' . $this->attendanceId . ',id,employee_id,' . $this->employeeId,
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $attendance = $this->attendanceId ? Attendance::find($this->attendanceId) : new Attendance;

        $attendance->employee_id = $this->employeeId;
        $attendance->date = $this->date;
        $attendance->in_1 = $this->checkIn;
        $attendance->out_1 = !empty($this->checkOut) ? $this->checkOut : null;
        $attendance->in_2 = null; // Assuming no second check-in
        $attendance->out_2 = null; // Assuming no second check-out
        $attendance->in_3 = null; // Assuming no third check-in
        $attendance->out_3 = null; // Assuming no third check-out
        $attendance->status = $this->status;
        $attendance->remarks = $this->remarks;
        $attendance->hours_worked = 0; // Default to 0, will be calculated later
        $hours = Carbon::parse($this->checkIn)->diffInMinutes(Carbon::parse($this->checkOut)) / 60;

        if ($hours > 0 && $hours < 5) {
            $this->status = 'half-day'; // Set status to half-day if hours worked is less than 5
        }
        if ($this->status === 'present' || $this->status === 'late') {
            $hours = $hours - 1; // Deduct 1 hour for present or late status
        }
        $attendance->hours_worked = $hours;

        if ($attendance->hours_worked < 0) {
            $attendance->hours_worked = 0; // Prevent negative hours
        }
        $attendance->source = 'manual';

        if($attendance->save()){
            log_activity('Attendance ' . ($this->attendanceId ? 'updated' : 'created') . ' for employee ID ' . $this->employeeId);
            session()->flash('message', $this->attendanceId ? 'Attendance updated successfully.' : 'Attendance created successfully.');

            $this->resetFields();
            $this->isOpen = false;
        }
    }
    public function sortBy($sort){
        $this->sortField = $sort;
        $this->sortDirection[$sort] = $this->sortDirection[$sort] == 'asc' ? 'desc' : 'asc';
    }

    public function delete(){
        $attendance = Attendance::find($this->confirmDelete);

        if ($attendance) {
            log_activity('Attendance deleted', 'Attendance record deleted for employee ID #' . $attendance->employee->employee_id, $attendance);
            session()->flash('message', 'Attendance deleted successfully.');
            $attendance->delete();
            $this->confirmDelete = false;
        }

    }

    // Other CRUD methods similar to EmployeeManagement...
    public function resetFields()
    {
        $this->employeeId = null;
        $this->date = null;
        $this->checkIn = null;
        $this->checkOut = null;
        $this->status = null;
        $this->remarks = null;
    }
}
