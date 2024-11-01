<?php

namespace App\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CreateInvoices::class,
        Commands\CheckTenantRentIsDue::class,
        Commands\SendListOfTenantsWhoDidntPay::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('create:invoices')->dailyAt('23:45');
        $schedule->command('create:expenses')->dailyAt('23:00');

        $schedule->command('notifications:leases-end')->dailyAt('23:55');

        //removed because it is in the create invoices routine
        //$schedule->command('notifications:check-tenant-rent-is-due')->dailyAt('23:55');
        //$schedule->command('notifications:check-tenant-unit-has-landlord-linked-finance')->dailyAt('23:55');

        $schedule->command('notifications:landlord-0-financial')->weekly()->sundays()->at('23:55');
        $schedule->command('notifications:send-list-of-tenants-who-did-not-pay')->dailyAt('23:55');
        $schedule->command('notifications:calendar-event-reminder')->dailyAt('00:10');

        $schedule->command('create:recurring-payments')->dailyAt('23:55');
        $schedule->command('command:check-subscriptions')->dailyAt('00:05');
        // $schedule->command('command:create-plans');

        // Artisan::call('notifications:leases-end'); // for test if cron is disabled
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
