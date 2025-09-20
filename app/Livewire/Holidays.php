<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Holiday;
use Livewire\WithPagination;

class Holidays extends Component
{
    use WithPagination;
    public $search = '';
    public $sortBy = 'date';
    public $sortDirection = [
        'name' => 'asc',
        'date' => 'asc',
    ];
    public $perPage = 10;

    public $isOpen, $holidayId, $name, $type, $description, $date, $confirmDelete=false;

    public $types = ['regular', 'special-non-working', 'special-working', 'company'];

    public function render()
    {
        $holidays = Holiday::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection[$this->sortBy])
            ->paginate($this->perPage);


        return view('livewire.holidays', compact('holidays'));
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection[$field] = $this->sortDirection[$field]  === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection[$field] = 'asc';
        }
    }

    public function create(){

        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id){
        $this->holidayId = $id;
        $holiday = Holiday::find($id);
        $this->name = $holiday->name;
        $this->date = $holiday->date;
        $this->type = $holiday->type;
        $this->description = $holiday->description;

        $this->isOpen = true;
    }

    public function store(){
        $data = $this->validate([
            'name' => 'required|string|max:190',
            'date' => 'required|date',
            'type' => 'required',
            'description' => 'nullable|string|max:255',
        ]);

        $data['updated_by'] = auth()->id();

        if(!$this->holidayId){
            $data['created_by'] = auth()->id();
        }
        // dd($data);
        $holiday = Holiday::updateOrCreate(
            ['id' => $this->holidayId],
            $data
        );
        log_activity('add_holiday', $this->holidayId ? 'Holiday Updated.' : 'Holiday Created.', $holiday, ['holiday' => $holiday->name]);
        session()->flash('message', $this->holidayId ? 'Holiday Updated.' : 'Holiday Created.');

        $this->resetInputFields();
        $this->isOpen = false;
    }

    public function delete(){
        if ($this->confirmDelete) {
            $holiday = Holiday::find($this->confirmDelete);
            if ($holiday) {
                $holiday->delete();
            }
            session()->flash('message', 'Holiday Deleted.');
            $this->confirmDelete = false;
        }
    }

    private function resetInputFields(){
        $this->name = '';
        $this->description = '';
        $this->date = '';
        $this->type = '';
        $this->holidayId = null;
    }
}
