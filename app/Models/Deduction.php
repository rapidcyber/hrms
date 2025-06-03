<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    public $fillable = [
        'id',
        'name',
        'code',
        'description',
        'type',
        'applies_to_all',
        'default_amount',
        'created_by',
        'updated_by'
    ];


    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function payrolls(){
        return $this->belongsToMany(Payroll::class);
    }
}
