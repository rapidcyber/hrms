<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    public function employee(){
        return $this->hasMany(Employee::class);
    }
}
