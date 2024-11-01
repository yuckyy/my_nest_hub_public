<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'start', 'end'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
