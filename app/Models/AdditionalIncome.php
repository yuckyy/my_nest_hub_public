<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalIncome extends Model
{
    //
    protected $fillable = [
        'description', 'amount', 'application_id'
    ];
}
