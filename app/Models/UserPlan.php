<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'stripe_subscription_id',
        'stripe_subscription_status',
        'coupon_code'
    ];

    public function subscriptionPlan()
    {
        return $this->hasOne('App\Models\SubscriptionPlan','id','plan_id');
    }

    public function coupon()
    {
        return $this->hasOne('App\Models\Coupon','code','coupon_code');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User','id','user_id');
    }
}
