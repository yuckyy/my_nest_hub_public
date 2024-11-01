<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'addon_id',
        'start_date',
        'end_date',
        'stripe_subscription_id',
        'stripe_subscription_status',
        'coupon_code',
    ];
}
