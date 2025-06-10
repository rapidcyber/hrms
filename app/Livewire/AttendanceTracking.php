<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\Employee;
use Livewire\WithPagination;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AttendanceImport;
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

    public function mount ()
    {
        $this->periodStart = now()->subMonth()->firstOfMonth()->format('Y-m-d');
        $this->periodEnd = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $attendances = Attendance::join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->where(function($query) {
            $query->where('employees.first_name', 'like', '%'. $this->search.'%')
                  ->orWhere('employees.last_name', 'like', '%'. $this->search.'%')
                  ->orWhere('employees.employee_id', 'like', '%' .$this->search.'%');
            })
            ->whereBetween('attendances.date', [$this->periodStart, $this->periodEnd])
            ->orderBy($this->sortField, is_array($this->sortDirection) ? ($this->sortDirection[$this->sortField] ?? 'desc') : $this->sortDirection)
            ->select('attendances.*')
            ->paginate(10);

        $employees = Employee::latest()->get();

        return view('livewire.attendance-tracking', ['attendances' => $attendances, 'employees'=> $employees]);
    }

    public function syncBiometricData()
    {
        ini_set('max_execution_time', 600); // 600 seconds
        $zk = new ZktecoLib('192.168.1.142', 4370); // Default port: 4370
        $zk->connect();
        $attendances = $zk->getAttendance();

        // Convert array to a Laravel Collection for easier manipulation

        // Generate sample attendance data for the last 2 months
        // $attendances = [];
        // $employeeIds = [12, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43];
        // $bioIdBase = 65000000000;
        // $startDate = Carbon::now()->subMonths(2)->startOfMonth();
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
                    if ($checkIn && Carbon::parse($checkIn)->gt(Carbon::parse($shift->time_in))) {
                        $status = 'late';
                    }

                    if (is_null($checkIn)) {
                        $status = 'absent';
                    }
                    $restDays = [
                        0 => 'Sunday',
                        1 => null,
                        2 => null,
                        3 => null,
                        4 => null,
                        5 => null,
                        6 => 'Saturday'
                    ];
                    if (in_array(Carbon::parse($attendance->date)->dayOfWeek(), array_keys(array_filter($restDays)))) {
                        $status = 'rest-day';
                    }
                    if ($employee->shift_id === 3) {
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

    public function edit($id){
        $this->isOpen = true;
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

    public function store()
    {

        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'date' => 'required|date|unique:attendances,date,' . $this->attendanceId . ',id,employee_id,' . $this->employeeId,
            'checkIn' => 'required',
            'checkOut' => 'required',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $attendance = $this->attendanceId ? Attendance::find($this->attendanceId) : new Attendance;

        $attendance->employee_id = $this->employeeId;
        $attendance->date = $this->date;
        $attendance->in_1 = $this->checkIn;
        $attendance->out_1 = $this->checkOut;
        $attendance->status = $this->status;
        $attendance->remarks = $this->remarks;
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
