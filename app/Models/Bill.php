<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    //
    public $timestamps = false;

    public function lease(){
        return $this->belongsTo(Lease::class, 'lease_id','id');
    }

    public function file() {
        return $this->hasOne('App\Models\File', 'id', 'file_id');
    }

    public function fileUrl()
    {
        return $this->file_id ? url('storage/bill/' . ($this->file->filename)) : null;
    }

    public function getPayedAttribute() {
        return number_format(Payment::where([['base_id',$this->id],['is_lease_pay',0]])->sum('amount'), 2, '.', '');
    }

    public function getBalanceAttribute() {
        return number_format($this->payed - $this->value, 2, '.', '');
    }

    public function getBillAmountAttribute() {
        return number_format($this->value - $this->payed, 2, '.', '') > 0 ? number_format($this->value - $this->payed, 2, '.', '') : 0;
    }
}
