<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use App\Models\Shift;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laradevsbd\Zkteco\Http\Library\ZktecoLib;

class EmployeeManagement extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $employeeId, $firstName, $lastName, $email, $phone, $dateOfBirth, $hireDate,
        $baseSalary, $position, $department, $employee_id, $shift, $rest_days = [],
        $address, $oldPhoto,$photo;
    public $isOpen = false;
    public $isEdit = false;
    public $next = false;
    public $confirmDelete = 0;
    public $search = '';
    public $sortField = 'employee_id';
    public $sortDirection = 'asc';
    public $weekMap = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ];

    public function render()
    {
        $employees = Employee::join('departments', 'employees.department_id', '=', 'departments.id')
            ->join('positions', 'employees.department_id', '=', 'positions.id')
            ->where('first_name', 'like', '%'.$this->search.'%')
            ->orWhere('last_name', 'like', '%'.$this->search.'%')
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"])
            ->orWhere('employee_id', 'like', '%'.$this->search.'%')
            ->select('employees.*')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        $positions = Position::all();
        $departments = Department::all();
        $shifts = Shift::all();
        return view('livewire.employee-management', compact('employees', 'positions', 'departments', 'shifts'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function validateStep1(){

        $data = [
            'employee_id' => 'required|unique:employees,employee_id',
            'firstName' => 'required',
            'lastName' => 'required',
            'position' => 'required',
            'department' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required',
            'dateOfBirth' => 'required|date',
            'hireDate' => 'required|date',
            'baseSalary' => 'required|numeric',
            'shift' => 'required',
        ];

        if($this->employeeId){
            $data['employee_id'] = 'required|unique:employees,employee_id,'.$this->employeeId;
            $data['email'] = 'required|email|unique:employees,email,'. $this->employeeId;
        }


        $this->validate($data);
        $this->next = true;
    }
    public function store()
    {
        $data = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->dateOfBirth,
            'hire_date' => $this->hireDate,
            'base_salary' => $this->baseSalary,
            'employee_id' => $this->employee_id,
            'position_id' => $this->position,
            'department_id' => $this->department,
            'shift_id' => $this->shift,
            'address' =>$this->address
        ];

        if(!empty($this->rest_days)){
            $data['rest_days'] = json_encode($this->rest_days);
        }

        if($this->photo){
            $name = Str::slug($this->employee_id.$this->lastName) . '.' . $this->photo->getClientOriginalExtension();
            $data['photo'] = $name;
            $this->photo->storeAs('photos', $name, 'public');
        }

        $employee = Employee::updateOrCreate(['id' => $this->employeeId], $data);

        $log = [
            'action' => $this->employeeId ? 'update_employee' : 'add_employee',
            'description' => 'Employee ' . $this->firstName. ' '.$this->lastName . ' ' . $this->employeeId ? 'updated.' : 'added.'
        ];

        log_activity($log['action'], $log['description'], $employee, ['employee' => $this->firstName. ' '.$this->lastName]);

        session()->flash('message',
            $this->employeeId ? 'Employee Updated Successfully.' : 'Employee Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $this->employeeId = $id;
        $this->employee_id = $employee->employee_id;
        $this->firstName = $employee->first_name;
        $this->lastName = $employee->last_name;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->dateOfBirth = $employee->date_of_birth;
        $this->hireDate = $employee->hire_date;
        $this->baseSalary = $employee->base_salary;
        $this->address = $employee->address;
        if($employee->rest_days);
            $this->rest_days = json_decode($employee->rest_days, true);
        $this->oldPhoto = $employee->photo ?? null;
        $this->position = $employee->position_id;
        $this->department = $employee->department_id;
        $this->shift = $employee->shift_id;
        // $this->photo = null;

        $this->openModal();
    }

    public function updatedPhoto()
    {
        // dd($this->photo->temporaryUrl());
        $this->validate([
            'photo' => 'image|max:2000', // 2MB Max
        ]);

    }

    public function delete()
    {
        $employee = Employee::find($this->confirmDelete);
        DB::beginTransaction();
        if ($employee->attendances()->exists()) {

            $employee->attendances()->delete();

        }
        if ($employee->payrolls()->exists()) {
            $employee->payrolls()->delete();
        }
        foreach ($employee->deductions as $deduction) {
            $deduction->delete();
        }
        $log = [
            'action' => 'delete_employee',
            'description' => '#'.$employee->employee_id . ' '. $employee->first_name. ' '.$employee->last_name . ' ' . 'deleted.'
        ];
        $employee->delete();

        log_activity($log['action'], $log['description'], $employee, ['employee' => $employee->first_name. ' '.$employee->last_name]);

        session()->flash('message', 'Employee Deleted Successfully.');
        DB::commit();



        $this->confirmDelete = 0;
    }

    public function sort($sort){
        $this->sortField = $sort;
        $this->sortDirection = ($this->sortDirection == 'asc') ? 'desc' : 'asc';
    }

    public function syncEmployees(){
        $zk = new ZktecoLib('192.168.1.142', 4370); // Default port: 4370
        $zk->connect();
        $users = $zk->getUser();

        try {
            DB::transaction(function () use ($users) {
                foreach($users as $user){
                    // check if employee exists
                    $employee = Employee::where('employee_id', $user[0])->first() ?? new Employee;
                    $employee->employee_id = $user[0];
                    $employeeName = explode('.',trim($user[1]));
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
            });
        } catch (\Throwable $th) {
            //throw $th;\
            dd($th);
        }

    }

    private function resetInputFields()
    {
        $this->employeeId = null;
        $this->employee_id = '';
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phone = '';
        $this->dateOfBirth = '';
        $this->hireDate = '';
        $this->baseSalary = '';
        $this->position = '';
        $this->department = '';
        $this->rest_days = [];
        $this->address = null;
        $this->photo = null;
        $this->next = false;
    }
}
