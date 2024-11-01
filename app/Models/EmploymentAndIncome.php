<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentAndIncome extends Model
{
    //
    protected $fillable = [
        'employment', 'income', 'application_id'
    ];
}
