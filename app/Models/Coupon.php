<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'discount'
    ];

    public function userPlans()
    {
        return $this->hasMany('App\Models\UserPlan','coupon_code','code');
    }

    public function getUsingCountAttribute()
    {
        return $this->userPlans()->count();
    }
}
