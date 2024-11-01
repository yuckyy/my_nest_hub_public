<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id','id');
    }

    public function file() {
        return $this->hasOne('App\Models\File', 'id', 'file_id');
    }

    public function fileUrl()
    {
        return $this->file_id ? url('storage/expenses/' . ($this->file->filename)) : null;
    }
}
