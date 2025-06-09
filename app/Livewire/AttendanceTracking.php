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

        $zk = new ZktecoLib('192.168.1.142', 4370); // Default port: 4370
        $zk->connect();
        // $users = $zk->getUser();
        $attendances = $zk->getAttendance();

        // Convert array to a Laravel Collection for easier manipulation
        $punches = collect($attendances)->map(function ($record) {
            return [
                'bio_id' => $record[0],
                'employee_id' => $record[1],
                'in_out_mode' => $record[2],
                // 'timestamp' => Carbon::parse($record[3]),
                'timestamp' => Carbon::parse($record[3]),
            ];
        });


            // 35830 => array:4 [▼
    //   "bio_id" => 66449550848
    //   "employee_id" => 20
    //   "in_out_mode" => 1
    //   "timestamp" =>
// Carbon
// \
// Carbon @1749459191
//  {#37467 ▶}
//     ]
//     35829 => array:4 [▼
//       "bio_id" => 66181111328
//       "employee_id" => 12
//       "in_out_mode" => 1
//       "timestamp" =>
// Carbon
// \
// Carbon @1749457350
//  {#37466 ▶}
//     ]
//     35828 => array:4 [▼
//       "bio_id" => 65912680016
//       "employee_id" => 25
//       "in_out_mode" => 1
//       "timestamp" =>
// Carbon
// \
// Carbon @1749457269
//  {#37465 ▶}
//     ]
//     35827 => array:4 [▼
//       "bio_id" => 65644240496
//       "employee_id" => 17
//       "in_out_mode" => 1
//       "timestamp" =>
// Carbon
// \
// Carbon @1749456933
//  {#37464 ▶}
//     ]


        dd($punches->sortByDesc('timestamp')->take(100));

        try {
            DB::transaction(function () use ($users, $attendances) {
                foreach($users as $user){
                    // check if employee exists
                    $employee = Employee::where('employee_id', $user[0])->first() ?? new Employee;
                    $employee->employee_id = $user[0];
                    $employeeName = explode('.',trim($user[0]));
                    $employee->first_name = $employeeName[0];
                    $employee->last_name = $employeeName[1] ?? '';
                    $employee->email = $user[0] .'changethis@email.com';
                    $employee->phone = '+639000000000';
                    $employee->date_of_birth = '2000-01-01';
                    $employee->hire_date = now()->format('Y-m-d');
                    $employee->base_salary = 0.00;
                    $employee->department_id = 5;
                    $employee->position_id = 6;
                    $employee->shift_id = 1;
                    $restDays = [
                        0=> 'Sunday',
                        1 => null,
                        2 => null,
                        3 => null,
                        4 => null,
                        5 => null,
                        6 => 'Saturday'
                    ];
                    $employee->rest_days = json_encode($restDays);
                    $employee->save();
                }
                $date = '';


                foreach($punches as $punch){
                    //check attendance exists






                }
            });
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }



        // $filePath = public_path('uploads/attendances.xls'); // or .csv

        // Excel::import(new AttendanceImport, $filePath);

        // log_activity('Biometric data synced', 'Biometric data synced via import', null, []);
        // session()->flash('message', 'Biometric data synced successfully.');
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
