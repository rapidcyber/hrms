<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Department;

class Departments extends Component
{
    public $departmentId, $name, $description;
    public $isOpen = false;
    public $confirmDelete = false;
    public $listeners = ['departmentCreated', 'departmentUpdated', 'departmentDeleted'];
    public function render()
    {
        $departments = Department::all();
        return view('livewire.departments', compact('departments'));
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
        $this->departmentId = '';
        $this->name = '';
        $this->description = '';
        $this->holidayId = '';
    }
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $department = $this->departmentId ? Department::find($this->departmentId) : new Department;

        $department->name = $this->name;
        $department->description = $this->description;



        if($department->save()){
            $log = [
                'action' => $this->departmentId ? 'update_department' : 'added_department',
                'description' => $this->departmentId ? 'Department updated' : 'Department created'
            ];
            log_activity($log['action'], $log['description'], $department, ['department'=>$department->name]);
            session()->flash('message', $this->departmentId ? 'Department updated successfully.' : 'Department created successfully.');
        }

        $this->closeModal();
    }
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->departmentId = $department->id;
        $this->name = $department->name;
        $this->description = $department->description;
        $this->openModal();
    }
    public function delete($id)
    {
        $department = Department::findOrFail($id);

        $msg = [
            'action' => 'error',
            'message' => 'Department has employees.'
        ];

        if($department->employees->isEmpty()){
            $msg = [
                'action' => 'message',
                'message' => 'Department deleted successfully.'
            ];
            $log = [
                'action' => 'delete_department',
                'description' => 'Department deleted'
            ];
            $department->delete();
            log_activity($log['action'], $log['description'], $department, ['department'=>$department->name]);
        }
        session()->flash($msg['action'], $msg['message']);
        $this->cancelDelete();
    }
    public function confirmDelete($id)
    {
        $this->confirmDelete = $id;
    }
    public function cancelDelete()
    {
        $this->confirmDelete = false;
    }
    public function resetForm()
    {
        $this->resetInputFields();
        $this->confirmDelete = false;
    }
    public function getListeners()
    {
        return [
            'departmentCreated' => 'resetForm',
            'departmentUpdated' => 'resetForm',
            'departmentDeleted' => 'resetForm',
        ];
    }
}
