<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\ThirteenthPay;
use Carbon\Carbon;

class Month13 extends Component
{
    use WithPagination;

    public $search = '', $sortField = 'id', $sortDirection = 'asc';
    public $date, $showProcessModal = 0, $employee_id, $summary = [];

    public function render()
    {
        $employees = Employee::where(function($q) {
            $q->where('first_name', 'LIKE', '%' . $this->search . '%')
            ->orWhere('last_name', 'LIKE', '%' . $this->search . '%')
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%" . $this->search . "%"]);
        })->orderBy($this->sortField, $this->sortDirection)
        ->paginate(10);

        $thirteenthPays = ThirteenthPay::all();

        return view('livewire.month13', compact('employees', 'thirteenthPays'));
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }




    public function process($id)
    {
        // Check if employee exists

        $employee = Employee::find($id);

        if (!$employee) {
            session()->flash('error', 'Employee not found.');
            return;
        }
        $this->showProcessModal = $id;

        $dailyRate = $employee->base_salary / 2 / 12;

        $attendances = $employee->attendances->filter(function ($attendance) {
            return Carbon::parse($attendance->date)->year == Carbon::parse($this->date)->year;
        });

        if ($attendances->isEmpty()) {
            session()->flash('error', 'Employee has no attendances for the selected year.');
            return;
        }
        // Count months attended
        $monthsAttended = $attendances->groupBy(function ($attendance) {
            return Carbon::parse($attendance->date)->format('M');
        });

        $totalMonthsAttended = $monthsAttended->count();

        $restDays = $employee->rest_days;

        $daysWorked = $attendances->filter(function ($attendance) use ($restDays) {
            return !in_array(Carbon::parse($attendance->date)->dayOfWeek, json_decode($restDays));
        })->count();



        $this->summary = [
            'days_worked' => $daysWorked,
            'total_months_attended' => $totalMonthsAttended,
            'daily_rate' => $dailyRate,
            'total_pay' => $this->calculate13thMonthPay($dailyRate, $daysWorked),
        ];

        // session()->flash('message', '13th month pay for ' . $employee->first_name . ' ' . $employee->last_name . ' has been processed successfully.');



        // $thirteenthPay = new ThirteenthPay;
        // $thirteenthPay->employee_id = $employee->id;
        // $thirteenthPay->date = $this->date ?? now();
        // $thirteenthPay->amount = $employee->base_salary;
        // $thirteenthPay->save();

    }

    public function save()
    {
        $employee = Employee::find($this->showProcessModal);
        $thirteenthPay = new ThirteenthPay;
        if (!empty($this->summary)){
            $thirteenthPay->employee_id = $employee->id;
            $thirteenthPay->payment_date = $this->date ?? now();
            $thirteenthPay->amount = $this->summary['total_pay'];

        }
        if($thirteenthPay->save()){
            session()->flash('message', '13th month pay for ' . $employee->first_name . ' ' . $employee->last_name . ' has been processed successfully.');
            $this->showProcessModal = 0;
            return;
        }
        $this->showProcessModal = 0;
        session()->flash('error', 'Failed to process 13th month pay for ' . $employee->first_name . ' ' . $employee->last_name . '.');
    }

    private function calculate13thMonthPay($dailyRate, $daysWorked) {
        // Calculate total basic salary earned
        $totalBasicSalary = $dailyRate * $daysWorked;

        // Compute 13th-month pay
        $thirteenthMonthPay = $totalBasicSalary / 12;

        return $thirteenthMonthPay;
    }
}
