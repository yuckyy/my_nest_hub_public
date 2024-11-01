<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonScreening extends Model
{
    protected $fillable = [
        'log_applicant',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function application() {
        return $this->belongsTo(Application::class);
    }

}
