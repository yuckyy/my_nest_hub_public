<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoveIn extends Model
{
    //
    public $timestamps = false;

    protected $fillable = [
        'memo', 'amount', 'due_on'
    ];
}
