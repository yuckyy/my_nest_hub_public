<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinanceUnit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'unit_id',
        'finance_id',
        'recurring_payment_day'
    ];

    public function unit() {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function finance() {
        return $this->hasOne('App\Models\Financial', 'id', 'finance_id');
    }
}
