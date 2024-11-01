<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'filename',
    ];

    public function getFile()
    {
        return $this->id . $this->ext;
    }

    public $timestamps = false;
}
