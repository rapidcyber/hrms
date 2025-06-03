<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Leave;
use App\Models\Employee;
use Carbon\Carbon;
use Livewire\WithPagination;
class LeaveManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'start_date';
    public $sortDirection = [
        'start_date' => 'desc',
        'employee_name' => 'asc',
        'type' => 'asc',
        'status' => 'asc',
    ];
    public $perPage = 10;
    public $isOpen = false;
    public $selectedLeaveId = null;
    public $confirmDelete = false;
    public $leaveTypes = ['sick', 'vacation', 'maternity', 'paternity', 'bereavement', 'unpaid'];
    // Form
    public $employee_id,
        $start_date,
        $end_date,
        $type,
        $status = 'pending',
        $reason,
        $updated_by,
        $created_by,
        $approved_by = 1;   // Default to 1 for the admin user

    public function render()
    {
        $leaves = Leave::with('employee')
            ->orderBy($this->sortBy, $this->sortDirection[$this->sortBy])
            ->paginate($this->perPage);
        $employees = Employee::all();
        $statuses = Leave::select('status')->distinct()->pluck('status');
        $today = Carbon::now()->format('Y-m-d');
        $upcomingLeaves = Leave::where('start_date', '>=', $today)
            ->with('employee')
            ->orderBy('start_date', 'asc')
            ->get();

        return view('livewire.leave-management', [
            'leaves' => $leaves,
            'employees' => $employees,
            'statuses' => $statuses,
            'upcomingLeaves' => $upcomingLeaves,
        ]);
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection[$field] = $this->sortDirection[$field] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection[$field] = 'asc';
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->isOpen = true;

    }
    public function edit($leaveId)
    {
        $this->selectedLeaveId = $leaveId;
        $leave = Leave::findOrFail($leaveId);
        $this->employeeId = $leave->employee_id;
        $this->startDate = $leave->start_date;
        $this->endDate = $leave->end_date;
        $this->type = $leave->type;
        $this->status = $leave->status;
        $this->reason = $leave->reason;
        $this->approvedById = $leave->approved_by;
        $this->isOpen = true;
    }
    public function delete()
    {
        $this->confirmDe;
        $this->dispatchBrowserEvent('confirmDeleteLeave');
    }

    public function store(){
        $this->updated_by = auth()->id();
        if (is_null($this->selectedLeaveId)) {
            $this->created_by = auth()->id();
        }

        if($this->status === 'approved') {
            $this->approved_by = auth()->id();
        }

        $data = [
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'status' => 'nullable|string',
            'reason' => 'nullable|string',
            'updated_by' => 'required|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
        ];

        if (is_null($this->selectedLeaveId)) {
            $data['created_by'] = 'required|exists:users,id';
        }

        // dd($this->created_by, $this->updated_by, $this->approved_by);
        $validatedData = $this->validate($data);


        Leave::updateOrCreate(
            ['id' => $this->selectedLeaveId],
            $validatedData
        );



        session()->flash('message', 'Leave request saved successfully.');
        $this->resetForm();
        $this->isOpen = false;
    }

    private function resetForm()
    {
        $this->employee_id = '';
        $this->start_date = null;
        $this->end_date = null;
        $this->type = '';
        $this->status = 'pending';
        $this->reason = '';
        $this->approved_by = null;
    }
}
