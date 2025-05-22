<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
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
        return $this->belongsToMany(Deduction::class);
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
}
