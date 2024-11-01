<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $plan = new SubscriptionPlan();
        $plan->name = 'Small';
        $plan->max_units = 10;
        $plan->price = 35.49;
        $plan->save();

        for ($i=1; $i < 4; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }

        $plan = new SubscriptionPlan();
        $plan->name = 'Medium';
        $plan->max_units = 20;
        $plan->price = 55.49;
        $plan->save();

        for ($i=1; $i < 4; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }

        $plan = new SubscriptionPlan();
        $plan->name = 'Large';
        $plan->max_units = 100;
        $plan->price = 105.49;
        $plan->save();

        for ($i=1; $i < 6; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }

        $plan = new SubscriptionPlan();
        $plan->name = 'Unlimited';
        $plan->period = Null;
        $plan->save();

        for ($i=1; $i < 7; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }
    }
}
