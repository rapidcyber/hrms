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

        // $attendances = [
        //     [66449550848, 20, 1, '2025-06-09 07:59:51'],
        //     [66449550848, 20, 1, '2025-06-09 08:59:51'],
        //     [66449550848, 20, 1, '2025-06-09 17:59:51'],
        //     [66181111328, 12, 1, '2025-06-09 08:55:50'],
        //     [66181111328, 12, 1, '2025-06-09 08:56:50'],
        //     [66181111328, 12, 1, '2025-06-09 16:56:50'],
        //     [65912680016, 25, 1, '2025-06-09 08:54:29'],
        //     [65912680016, 25, 1, '2025-06-09 17:54:29'],
        //     [65644240496, 17, 1, '2025-06-09 09:48:53'],
        //     [65644240496, 17, 1, '2025-06-09 15:48:53'],
        //     [65375800976, 18, 1, '2025-06-09 09:47:11'],
        //     [65375800976, 18, 1, '2025-06-09 17:47:11'],
        //     [65109361456, 19, 1, '2025-06-09 09:46:30'],
        //     [65109361456, 19, 1, '2025-06-09 18:46:30'],
        //     [64838921936, 21, 1, '2025-06-09 09:45:49'],
        //     [64838921936, 21, 1, '2025-06-09 15:45:49'],
        //     [64570482416, 22, 1, '2025-06-09 09:44:48'],
        //     [64570482416, 22, 1, '2025-06-09 16:44:48'],
        //     [64302042896, 23, 1, '2025-06-09 09:43:47'],
        //     [64302042896, 23, 1, '2025-06-09 17:43:47'],
        //     [64033503376, 24, 1, '2025-06-09 08:42:46'],
        //     [64033503376, 24, 1, '2025-06-09 17:42:46'],
        //     [63764963856, 26, 1, '2025-06-09 08:41:45'],
        //     [63764963856, 26, 1, '2025-06-09 16:41:45'],
        //     [63496424336, 27, 1, '2025-06-09 08:40:44'],
        //     [63227884816, 28, 1, '2025-06-09 08:39:43'],
        //     [62959345296, 29, 1, '2025-06-09 08:38:42'],
        //     [62690805776, 30, 1, '2025-06-09 08:37:41'],
        //     [62422266256, 31, 1, '2025-06-09 08:36:40'],
        //     [62153726736, 32, 1, '2025-06-09 08:35:39'],
        //     [61885187216, 33, 1, '2025-06-09 08:34:38'],
        //     [61616647696, 34, 1, '2025-06-09 08:33:37'],
        //     [61348108176, 35, 1, '2025-06-09 07:32:36'],
        //     [61099568656, 36, 1, '2025-06-09 08:31:35'],
        //     [61099568656, 36, 1, '2025-06-09 05:31:35'],
        //     [60811029136, 37, 1, '2025-06-09 08:30:34'],
        //     [60542489616, 38, 1, '2025-06-09 08:29:33'],
        //     [60273950096, 39, 1, '2025-06-09 08:28:32'],
        //     [60005410576, 40, 1, '2025-06-09 08:27:31'],
        //     [59736871056, 41, 1, '2025-06-09 08:26:30'],
        //     [59468331536, 42, 1, '2025-06-09 08:25:29'],
        //     [59199792016, 43, 1, '2025-06-09 08:24:28'],
        //     // Add more records as needed
        // ];
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
                foreach ($punches as $punch) {
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
