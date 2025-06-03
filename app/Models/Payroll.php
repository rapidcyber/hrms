<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    // public $fillable = [
    //     'employee_id',
    //     'cutoff_id',
    //     'base_salary',
    //     'overtime',
    //     'bonus',
    //     'total_deductions',
    //     'net_salary',
    //     'created_by',
    //     'updated_by',
    // ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function deductions()
    {
        return $this->hasMany(PayrollDeduction::class);
    }
    public function getGrossSalary()
    {
        return $this->base_salary + $this->overtime + $this->bonus;
    }
    public function getNetSalary()
    {
        return $this->getGrossSalary() - $this->total_deductions;
    }
    public function getTotalDeductions()
    {
        return $this->deductions->sum('amount');
    }
}
