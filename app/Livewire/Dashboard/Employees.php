<?php

namespace App\Livewire\Dashboard;
use App\Models\Employee;

use Livewire\Component;

class Employees extends Component
{
    public function render()
    {

        $activeEmployees = Employee::all()->count();

        return view('livewire.dashboard.employees', compact('activeEmployees'));
    }
}
