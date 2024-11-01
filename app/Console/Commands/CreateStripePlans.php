<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionPlan;
use App\Services\StripeSubscriptionService;

class CreateStripePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $plans = SubscriptionPlan::whereNotNull('price')->get();
        foreach ($plans as $plan) {
            $product = $this->subscriptionService->createProduct($plan->name);
            $data = [
                'amount' => $plan->price*100,
                'currency' => 'usd',
                'interval' => 'month',
                'product' => $product->id,
                'trial_period_days' => 30
            ];
            $stripePlan = $this->subscriptionService->createPlan($data);
            $plan->update([
               'stripe_plan_id' => $stripePlan->id,
            ]);
        }
    }
}
