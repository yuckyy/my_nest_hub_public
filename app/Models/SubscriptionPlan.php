<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'max_units',
        'price',
        'period',
        'stripe_plan_id',
        'show_plan',
        'stripe_product_id'
    ];

    public function hasOption($optionId)
    {
        return PlanOption::where([['plan_id',$this->id],['option_id',$optionId]])->first();
    }

    public static function freeTrial(){
        $freePlan = new self;
        $freePlan->name = "Free Trial";
        $freePlan->max_units = 10;
        return $freePlan;
    }
}
