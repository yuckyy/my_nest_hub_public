<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class References extends Model
{
    //
    protected $fillable = [
        'name', 'email', 'phone', 'application_id'
    ];
}
