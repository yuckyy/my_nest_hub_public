<?php

namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notification;
use App\Notifications\FinancialNotifications;

class landlordHas0 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:landlord-0-financial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        //TODO optimize db query
        $lanlords = User::where('no_financial_notification_sent',false)->get()->filter(function($item) {
            if ($item->financialAccounts->count() == 0) {
                return $item->isLandlord();
            }
        });

        foreach($lanlords as $user) {
            $user->notify(new FinancialNotifications());
            $user->no_financial_notification_sent = true;
            $user->save();
        }
    }
}
