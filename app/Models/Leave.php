<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'id',
        'employee_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'type',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
