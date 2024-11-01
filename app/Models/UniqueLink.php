<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniqueLink extends Model
{
    //
    protected $fillable = [
        'link',
        'model_id',
        'model_type'
    ];

    public function model()
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }
}
