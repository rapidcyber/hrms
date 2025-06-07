<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    public function employee()
    {
        return $this->belongs(Employee::class);
    }
}
