<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPlan;

class SubscriptionUnlimitedPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $plan = new SubscriptionPlan();
        $plan->id = 5;
        $plan->name = 'Unlimited+';
        $plan->max_units = 150;
        $plan->price = 155.49;
        $plan->show_plan = 0;
        $plan->save();

        for ($i=1; $i < 7; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }

        $plan = new SubscriptionPlan();
        $plan->id = 6;
        $plan->name = 'Unlimited++';
        $plan->max_units = 200;
        $plan->price = 200.49;
        $plan->show_plan = 0;
        $plan->save();

        for ($i=1; $i < 7; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }

        $plan = new SubscriptionPlan();
        $plan->id = 7;
        $plan->name = 'Unlimited+++';
        $plan->max_units = 250;
        $plan->price = 255.49;
        $plan->show_plan = 0;
        $plan->save();

        for ($i=1; $i < 7; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }

        $plan = new SubscriptionPlan();
        $plan->id = 8;
        $plan->name = 'Unlimited++++';
        $plan->max_units = 300;
        $plan->price = 300.49;
        $plan->show_plan = 0;
        $plan->save();

        for ($i=1; $i < 7; $i++) {
            DB::table('plan_options')->insert([
                'plan_id' => $plan->id,
                'option_id' => $i,
            ]);
        }
    }
}
