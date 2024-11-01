<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPlan;
use App\Services\StripeSubscriptionService;
use Carbon\Carbon;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check-subscriptions';

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
        $plans = UserPlan::whereDate('end_date',Carbon::today()->subDay())->get();
        foreach ($plans as $plan) {
            try {
                $stripePlan = $this->subscriptionService->retrieveSubscription($plan->stripe_subscription_id);

                $plan->update([
                   'stripe_subscription_status' => $stripePlan->status,
                ]);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }
    }
}
