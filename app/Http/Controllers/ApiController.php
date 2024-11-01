<?php

namespace App\Http\Controllers;

use App\Models\PlanOption;
use App\Models\SubscriptionOption;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller
{
    //

    public function plans() {
        $plans = SubscriptionPlan::where('show_plan',1)->orderby('id')->get();

        $plansArray = [];
        foreach($plans as $plan){
            $p = [];
            $p['id'] = $plan->id;
            $p['name'] = $plan->name;
            $p['period'] = $plan->period;
            $p['max_units'] = $plan->max_units;
            $p['price'] = $plan->price;

            $options = SubscriptionOption::join('plan_options', 'option_id', '=', 'subscription_options.id')
                ->where('plan_id',$plan->id)
                ->select(['subscription_options.name'])->orderby('subscription_options.id')->get();
            $o = [];
            foreach($options as $option){
                $o[] = $option->name;
            }
            $p['options'] = implode(',',$o);
            $plansArray[$plan->id] = $p;
        }

        return Response::json($plansArray);
    }
}
