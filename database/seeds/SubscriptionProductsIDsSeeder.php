<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPlan;

class SubscriptionProductsIDsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $plans = SubscriptionPlan::get();

        if (env('STRIPE_ENV') !== null && env('STRIPE_ENV') == 'sandbox') {
            $varPart = "STRIPE_TEST_PRODUCT_ID_";
        } elseif (env('STRIPE_ENV') !== null && env('STRIPE_ENV') == 'live') {
            $varPart = "STRIPE_PRODUCT_ID_";
        }
        foreach ($plans as $plan) {
            if(env($varPart.$plan->id) !== null && env($varPart.$plan->id) !== '') {
                $plan->update([
                    'stripe_product_id' => env($varPart.$plan->id)
                ]);
            }
        }
    }
}
