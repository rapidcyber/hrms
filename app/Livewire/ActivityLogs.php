<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ActivityLog;

class ActivityLogs extends Component
{
    public function render()
    {
        $activityLogs = ActivityLog::latest()->take(10)->get();

        return view('livewire.activity-logs', compact('activityLogs'));
    }
}
