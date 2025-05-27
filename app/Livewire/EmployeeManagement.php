<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use App\Models\Shift;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class EmployeeManagement extends Component
{
    use WithPagination;

    public $employeeId, $firstName, $lastName, $email, $phone, $dateOfBirth, $hireDate,
        $baseSalary, $position, $department, $employee_id, $shift;
    public $isOpen = false;
    public $isEdit = false;
    public $confirmDelete = 0;
    public $search = '';
    public $sortField = 'employee_id';
    public $sortDirection = 'asc';

    public function render()
    {
        $employees = Employee::where('first_name', 'like', '%'.$this->search.'%')
                            ->orWhere('last_name', 'like', '%'.$this->search.'%')
                            ->orWhere('employee_id', 'like', '%'.$this->search.'%')
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

    private function resetInputFields()
    {
        $this->employeeId = '';
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
    }

    public function store()
    {
        $this->validate([
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
        ]);

        Employee::updateOrCreate(['id' => $this->employeeId], [
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
        ]);

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

        $this->openModal();
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
        $employee->deductions()->detach();
        $employee->delete();
        session()->flash('message', 'Employee Deleted Successfully.');
        DB::commit();



        $this->confirmDelete = 0;
    }

    public function sort($sort){
        $this->sortField = $sort;
        $this->sortDirection = ($this->sortDirection == 'asc') ? 'desc' : 'asc';
    }
}
