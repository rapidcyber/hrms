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
    }
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        Department::updateOrCreate(
            ['id' => $this->departmentId],
            ['name' => $this->name, 'description' => $this->description]
        );

        session()->flash('message', $this->departmentId ? 'Department updated successfully.' : 'Department created successfully.');
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
        $department->delete();
        session()->flash('message', 'Department deleted successfully.');
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
