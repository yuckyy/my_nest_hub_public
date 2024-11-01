<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pets extends Model
{
    //
    protected $fillable = [
       'description', 'application_id', 'pets_type_id'
    ];

    public function type() {
        return $this->belongsTo(PetsTypes::class, 'pets_type_id');
    }
}
