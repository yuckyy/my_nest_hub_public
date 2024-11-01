<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmenitiesStructure extends Model
{
    public function structures() {
        return $this->hasMany(
            'App\Models\AmenitiesStructure',
            'parent',
            'id'
        );
    }

    public $timestamps = false;
}
