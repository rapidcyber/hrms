<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Deduction;

class Deductions extends Component
{
        //    $table->string('name');
        //     $table->string('code')->unique()->nullable();
        //     $table->enum('type', ['tax', 'benefits', 'voluntary', 'loan', 'other','custom'])->default('other');
        //     $table->enum('calculation_type', ['fixed', 'percentage'])->default('fixed');
        //     $table->decimal('default_amount', 10, 2);
        //     $table->decimal('min_amount', 10, 2)->nullable();
        //     $table->decimal('max_amount', 10, 2)->nullable();
        //     $table->boolean('tax_deductible')->default(false);
        //     $table->boolean('applies_to_all')->default(false);
        //     $table->date('effective_from')->nullable();
        //     $table->date('effective_until')->nullable();
        //     $table->text('description')->nullable();
        //     $table->json('metadata')->nullable();
        //     $table->foreignId('created_by')->constrained('users')->nullable();
        //     $table->foreignId('updated_by')->constrained('users')->nullable();


    public $isOpen = false, $deductionId, $confirDelete=false, $name, $code, $type,$calculation_type, $description;
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
        $this->code = $this->code;
        $deduction = Deduction::find($id);

        $this->name = $deduction->name;
        $this->description = $deduction->dedscription;
    }

    private function resetFields(){
        $this->name = '';
        $this->code = '';
        $this->type = 'tax';
        $this->calculation_type = 'fixed';
        $this->default_amount = null;
        $this->description = '';
    }
}
