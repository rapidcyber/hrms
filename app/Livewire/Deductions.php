<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Deduction;

class Deductions extends Component
{

    public $isOpen = false, $deductionId, $confirmDelete=false, $name, $code, $type,$calculation_type, $description, $apply_all;
    public $types = ['tax', 'benefits', 'voluntary', 'loan', 'other','custom'];

    public function render()
    {
        $deductions = Deduction::all();
        return view('livewire.deductions', compact('deductions'));
    }

    public function create(){
        $this->isOpen = true;
        $this->resetFields();
    }

    public function edit($id){
        $this->deductionId = $id;
        $this->isOpen = true;
        $deduction = Deduction::find($id);

        $this->name = $deduction->name;
        $this->code = $deduction->code;
        $this->type = $deduction->type;
        $this->apply_all = $deduction->applies_to_all;
        $this->description = $deduction->description;

    }

    public function delete(){
        $deduction = Deduction::find($this->confirmDelete);
        if($deduction->employees()->count() > 0){
            session()->flash('error', 'This deduction cannot be deleted as it is associated with employees.');
            return;
        }
        $deduction->delete();
        session()->flash('message', 'Deduction deleted successfully.');
        $this->confirmDelete = false;
    }

    public function store(){
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3',
            'description' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'default_amount' => 0, // Assuming default amount is set to 0 initially
            'description' => $this->description,
            'applies_to_all' => $this->apply_all
        ];

        if($this->deductionId){
            $data['updated_by'] = auth()->user()->id;
            $data['updated_at'] = now();
        } else {
            $data['created_by'] = auth()->user()->id;
            $data['updated_by'] = auth()->user()->id;
            $data['created_at'] = now();
            $data['updated_at'] = now();
        }

        Deduction::updateOrCreate(['id' => $this->deductionId], $data);

        session()->flash('message', $this->deductionId ? 'Deduction updated successfully.' : 'Deduction created successfully.');
        $this->isOpen = false;
    }

    private function resetFields(){
        $this->deductionId = null;
        $this->name = '';
        $this->code = '';
        $this->type = 'other';
        $this->apply_all = false;
        $this->description = '';
    }
}
