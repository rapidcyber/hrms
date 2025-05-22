<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }
}
