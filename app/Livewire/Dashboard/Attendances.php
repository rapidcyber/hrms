<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Attendance;

class Attendances extends Component
{
    public function render()
    {
        $attendances = Attendance::latest()->take(10)->get();
        return view('livewire.dashboard.attendances', compact('attendances'));
    }
}
