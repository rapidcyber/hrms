<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Payroll;

class Payrolls extends Component
{
    public function render()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $payrolls = Payroll::whereBetween('period_start', [$start, $end]);

        return view('livewire.dashboard.payrolls', compact('payrolls'));
    }
}
