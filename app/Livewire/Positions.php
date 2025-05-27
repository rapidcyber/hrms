<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Position;
use Livewire\WithPagination;

class Positions extends Component
{
    use WithPagination;
    public $name;
    public $description;
    public $positionID;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $showModal = false;
    public $confirmDelete = false;
    public $listeners = ['positionAdded', 'positionUpdated'];
    public function positionAdded($position)
    {
        $this->resetForm();
        session()->flash('message', 'Position added successfully.');
    }
    public function positionUpdated($position)
    {
        $this->resetForm();
        session()->flash('message', 'Position updated successfully.');
    }
    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->positionId = null;
        $this->isEditMode = false;
    }
    public function edit($positionId)
    {
        $this->showModal = true;
        $position = Position::find($positionId);
        if ($position) {
            $this->name = $position->name;
            $this->description = $position->description;
            $this->positionID = $position->id;

        }
    }
    public function delete($positionId)
    {
        $position = Position::find($positionId);

        if($position->employees->isNotEmpty()){
            session()->flash('message', 'Delete failed because the position has employees.');
            return;
        }

        if ($position) {
            $position->delete();
            session()->flash('message', 'Position deleted successfully.');
        }
    }
    public function savePosition()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($this->isEditMode) {
            $position = Position::find($this->positionId);
            if ($position) {
                $position->update([
                    'name' => $this->name,
                    'description' => $this->description,
                ]);
                $this->emit('positionUpdated', $position);
            }
        } else {
            Position::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            $this->emit('positionAdded', null);
        }

        $this->resetForm();
    }
    public function render()
    {
        $positions = Position::orderBy($this->sortField, $this->sortDirection)->paginate(10);

        return view('livewire.postions', compact('positions'));
    }
    public function sort($sort){
        $this->sortField = $sort;
        $this->sortDirection = ($this->sortDirection == 'asc') ? 'desc' : 'asc';
    }

    public function create(){
        $this->showModal = true;
    }
}
