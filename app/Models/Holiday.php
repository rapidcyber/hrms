<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'id',
        'name',
        'date',
        'type',
        'description',
        'created_by',
        'updated_by'
    ];
}
