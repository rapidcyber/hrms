<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'department_id',
        'position_id',
        'shift_id',
        'date_of_birth',
        'hire_date',
        'rest_days',
        'base_salary',
        'photo',
        'phone',
        'address'
        // add other fields as needed
    ];
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function scopeFilter($query, array $filters)
    {
        if ($filters['search'] ?? false) {
            $query->where(function ($query) use ($filters) {
                $query->where('first_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('employee_id', 'like', '%' . $filters['search'] . '%');
            });
        }
    }
    public function scopeDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
    public function scopePosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
    public function scopeWithDeductions($query)
    {
        return $query->with('deductions');
    }
    public function scopeWithAttendance($query)
    {
        return $query->with('attendances');
    }
    public function scopeWithPayroll($query)
    {
        return $query->with('payrolls');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
