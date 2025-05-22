<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;

class EmployeeManagement extends Component
{
    use WithPagination;

    public $employeeId, $firstName, $lastName, $email, $phone, $dateOfBirth, $hireDate, $baseSalary;
    public $isOpen = false;
    public $search = '';

    public function render()
    {
        $employees = Employee::where('first_name', 'like', '%'.$this->search.'%')
                            ->orWhere('last_name', 'like', '%'.$this->search.'%')
                            ->orWhere('employee_id', 'like', '%'.$this->search.'%')
                            ->paginate(10);

        return view('livewire.employee-management', ['employees' => $employees]);
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
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phone = '';
        $this->dateOfBirth = '';
        $this->hireDate = '';
        $this->baseSalary = '';
    }

    public function store()
    {
        $this->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required',
            'dateOfBirth' => 'required|date',
            'hireDate' => 'required|date',
            'baseSalary' => 'required|numeric',
        ]);

        Employee::updateOrCreate(['id' => $this->employeeId], [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->dateOfBirth,
            'hire_date' => $this->hireDate,
            'base_salary' => $this->baseSalary,
            'employee_id' => 'EMP'.rand(1000, 9999),
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
        $this->firstName = $employee->first_name;
        $this->lastName = $employee->last_name;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->dateOfBirth = $employee->date_of_birth;
        $this->hireDate = $employee->hire_date;
        $this->baseSalary = $employee->base_salary;

        $this->openModal();
    }

    public function delete($id)
    {
        Employee::find($id)->delete();
        session()->flash('message', 'Employee Deleted Successfully.');
    }
}
