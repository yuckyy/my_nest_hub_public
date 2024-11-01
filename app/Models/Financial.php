<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Financial extends Model
{
    use SoftDeletes;
    
    protected $table = 'financial';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'nickname',
        'finance_order',
        'holder_name',
        'finance_type',
        'source_id',
        'last4',
        'fingerprint',
        'exp_date',
        'billing_address',
        'billing_address_2',
        'city',
        'state',
        'zip',
        'connected',
        'paypal_email',
        'identity_id',
        'funding_source_url'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($finance) {
             $finance->finance_units()->delete();
        });
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function finance_units() {
        return $this->hasMany('App\Models\FinanceUnit', 'finance_id', 'id');
    }

    public function getFullAddressAttribute()
    {
        return $this->billing_address . ' ' . $this->billing_address_2 . ' ' . $this->city . ' ' . $this->state . ' ' . $this->zip;
    }

    public function isLinked($uid)
    {
        if (FinanceUnit::where([['finance_id',$this->id],['unit_id',$uid]])->first()) {
            return true;
        }
        return false;
    }
}
