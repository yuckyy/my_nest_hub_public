<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\FinanceUnit;
use App\Notifications\TenantUnitHasLinkedFinanceNotification;
use Illuminate\Console\Command;

class CheckTenantUnitHasLandlordLinkedFinance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check-tenant-unit-has-landlord-linked-finance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Landlord linked bank to Tenants unit and send notifications if required';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tenants = User::get()->filter(function($item) {
            if ($item->financialAccounts->count() == 0) {
                return $item->isTenant();
            }
        });

        foreach ($tenants as $t) {
            foreach ($t->leases as $lease) {
                if ($lease->deleted_at === null) {
                    $user = $lease->unit->property->user;
                    if (FinanceUnit::where([['user_id',$user->id],['unit_id',$lease->unit->id]])
                            ->whereDate('created_at','>=',\Carbon\Carbon::today()->subDays(5))->first()) {
                        $t->notify(new TenantUnitHasLinkedFinanceNotification($user));
                    }
                }
            }
        }

        \Log::info("Notified Success!");
    }
}
