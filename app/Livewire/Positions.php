<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Position;
use Livewire\WithPagination;

class Positions extends Component
{
    use WithPagination;
    public $name;
    public $type = 2; // 1 for daily 2 for fixed monthly
    public $description;
    public $positionId;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $showModal = false;
    public $confirmDelete = false;
    public $listeners = ['positionAdded', 'positionUpdated'];
    public $isEditMode = false;
    public function positionAdded($position)
    {
        $this->resetForm();
        session()->flash('message', 'Position added successfully.');
        $this->showModal = false;
    }
    public function positionUpdated($position)
    {
        $this->resetForm();
        $this->showModal = false;
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
        $this->resetForm();
        $this->showModal = true;
        $position = Position::find($positionId);
        $this->isEditMode = true;
        if ($position) {
            $this->name = $position->name;
            $this->type = $position->level;
            $this->description = $position->description;
            $this->positionId = $position->id;

        }
    }
    public function delete()
    {
        $position = Position::find($this->confirmDelete);

        $msg = [
            'action' => 'error',
            'message' => 'Delete failed because the position has employees.'
        ];

        if($position->employees->isEmpty()){
            $msg = [
                'action' => 'message',
                'message' => 'Position deleted successfully.'
            ];
            $log = [
                'action' => 'delete_Position',
                'description' => 'Position deleted'
            ];
            if($position->delete()){
                log_activity($log['action'], $log['description'], $position, ['Position'=>$position->name]);
            }
        }
        session()->flash($msg['action'], $msg['message']);
        $this->confirmDelete = 0;
    }
    public function savePosition()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|integer|min:1|max:5', // Assuming levels are between 1 and 5
        ]);

        if ($this->isEditMode) {
            $position = Position::find($this->positionId);
            if ($position) {
                $position->name = $this->name;
                $position->description = $this->description;
                $position->level = $this->type;
                if($position->save()){
                    $log = [
                        'action' => 'update_Position',
                        'description' => 'Position updated'
                    ];
                    log_activity($log['action'], $log['description'], $position, ['Position'=>$position->name]);
                    $this->dispatch('positionUpdated', $position);
                } else {
                    session()->flash('error', 'Failed to update position.');
                }



            }
        } else {
            Position::create([
                'name' => $this->name,
                'description' => $this->description,
                'level' => $this->type,
            ]);
            $this->dispatch('positionAdded', null);
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
        $this->resetForm();
    }
}
