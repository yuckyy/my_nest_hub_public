<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionPlan;
use App\Models\Addon;
use App\Services\StripeSubscriptionService;

class CreateAddonScreening extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addon-screening';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Screening Addon';

    private $subscriptionService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StripeSubscriptionService $subscriptionService)
    {
        parent::__construct();

        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = 'screening';
        $title = 'Tenant Screening Service';
        $description = 'Ability to send screening request to customer via tenant report';
        $price = 5.99;

        $addon = Addon::where('name',$name)->first();

        if(!empty($addon)){
            $addon->name = $name;
            $addon->title = $title;
            $addon->description = $description;
            $addon->price = $price;
            if( !empty(env('ADDON_SCREENING_STRIPE_PLAN_ID')) ){
                $addon->stripe_plan_id = env('ADDON_SCREENING_STRIPE_PLAN_ID');
            }
            if( !empty(env('ADDON_SCREENING_STRIPE_PRODUCT_ID')) ){
                $addon->stripe_product_id = env('ADDON_SCREENING_STRIPE_PRODUCT_ID');
            }
            $addon->save();

            return;
        }

        if(
            !empty(env('ADDON_SCREENING_STRIPE_PLAN_ID')) &&
            !empty(env('ADDON_SCREENING_STRIPE_PRODUCT_ID'))
        ){
            $addon = New Addon;
            $addon->name = $name;
            $addon->title = $title;
            $addon->description = $description;
            $addon->price = $price;
            $addon->stripe_plan_id = env('ADDON_SCREENING_STRIPE_PLAN_ID');
            $addon->stripe_product_id = env('ADDON_SCREENING_STRIPE_PRODUCT_ID');
            $addon->save();

            return;
        }

        $addon = New Addon;
        $addon->name = $name;
        $addon->title = $title;
        $addon->description = $description;
        $addon->price = $price;

        $product = $this->subscriptionService->createProduct($addon->name);
        $data = [
            'amount' => $addon->price*100,
            'currency' => 'usd',
            'interval' => 'month',
            'product' => $product->id,
            'trial_period_days' => 30
        ];
        $stripePlan = $this->subscriptionService->createPlan($data);

        $addon->stripe_plan_id = $stripePlan->id;
        $addon->stripe_product_id = $product->id;

        $addon->save();

        print "Success. Please Add ENV variables:\n";
        print "ADDON_SCREENING_STRIPE_PLAN_ID=".$addon->stripe_plan_id."\n";
        print "ADDON_SCREENING_STRIPE_PRODUCT_ID=".$addon->stripe_product_id."\n";

        return;
    }
}
