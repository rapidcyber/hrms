<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'check_in',
        'check_out',
        'status',
        'source',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function scopeFilter($query, array $filters)
    {
        if ($filters['search'] ?? false) {
            $query->where(function ($query) use ($filters) {
                $query->whereHas('employee', function ($query) use ($filters) {
                    $query->where('first_name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('employee_id', 'like', '%' . $filters['search'] . '%');
                });
            });
        }
    }
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_in', [$startDate, $endDate]);
    }
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', now()->format('Y-m-d'));
    }
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in', [now()->startOfWeek(), now()->endOfWeek()]);
    }
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('check_in', now()->month);
    }
    public function scopeThisYear($query)
    {
        return $query->whereYear('check_in', now()->year);
    }
    public function scopeLastWeek($query)
    {
        return $query->whereBetween('check_in', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
    }
    public function scopeLastMonth($query)
    {
        return $query->whereMonth('check_in', now()->subMonth()->month);
    }
    public function scopeLastYear($query)
    {
        return $query->whereYear('check_in', now()->subYear()->year);
    }
    public function scopeBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_in', [$startDate, $endDate]);
    }
    public function scopeTodayCheckIn($query)
    {
        return $query->whereDate('check_in', now()->format('Y-m-d'));
    }
    public function scopeTodayCheckOut($query)
    {
        return $query->whereDate('check_out', now()->format('Y-m-d'));
    }
    public function scopeCheckIn($query)
    {
        return $query->where('check_in', '!=', null);
    }
    public function scopeCheckOut($query)
    {
        return $query->where('check_out', '!=', null);
    }
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }
    public function scopeEarlyLeave($query)
    {
        return $query->where('status', 'early_leave');
    }
    public function scopeBiometric($query)
    {
        return $query->where('source', 'biometric');
    }
    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }
}
