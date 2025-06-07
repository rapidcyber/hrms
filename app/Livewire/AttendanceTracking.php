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
        $filePath = public_path('uploads/attendances.xls'); // or .csv

        Excel::import(new AttendanceImport, $filePath);

        log_activity('Biometric data synced', 'Biometric data synced via import', null, []);
        session()->flash('message', 'Biometric data synced successfully.');
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
            log_activity('Attendance deleted', 'Attendance record deleted for employee ID ' . $attendance->employee->employee_id, $attendance->id, []);
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
