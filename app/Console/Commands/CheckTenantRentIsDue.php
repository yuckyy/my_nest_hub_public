<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\TenantRentIsDueSoonNotification;
use Illuminate\Console\Command;

class CheckTenantRentIsDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check-tenant-rent-is-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tenants rent is due and send notifications if required';

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
        //DESABLED IN KERNEL

        //Invoicees not fully payed
        $invoices = Invoice::get()->filter(function($item) {
            return $item->balance < 0;
        });
        foreach ($invoices as $item) {
            $lease = $item->lease;
            $tenant = User::where('email',$lease->email)->first();
            if ($lease && $lease->deleted_at === null) {
                $tenant->notify(new TenantRentIsDueSoonNotification());
            }
        }

        \Log::info("Success!");
    }
}
