<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Notifications\ListOfTenantsWhoDidntPay;
use Illuminate\Support\Facades\DB;

class SendListOfTenantsWhoDidntPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-list-of-tenants-who-did-not-pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Landlord give notify with list of tenants who did not pay rent on due date';

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
        $lanlords = User::get()->filter(function($item) {
            return $item->isLandlord();
        });
        foreach($lanlords as $user) {
            $query1 = DB::table('invoices');
            $query1->leftJoin(
                DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
                'invoices.id', '=', 'p.invoice_id'
            );
            //invoice based on lease
            $query1->leftJoin('leases', 'leases.id', '=', 'invoices.base_id');
            $query1->leftJoin('units', 'units.id', '=', 'leases.unit_id');
            $query1->leftJoin('properties', 'properties.id', '=', 'units.property_id');
            $query1->leftJoin('states', 'states.id', '=', 'properties.state_id');
            //$query1->whereNull('leases.end_date');
            $query1->where('is_lease_pay', '=', '1');
            $query1->where('properties.user_id', '=', $user->id);
            $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
            $query1->where('leases.monthly_due_date', '=', Carbon::now()->addDay()->format('d'));
            $query1->select(
                'leases.id as lease_id'
                //'invoices.id',
                //'invoices.due_date',
                //'leases.unit_id',
                //'leases.monthly_due_date'
            );
            $lateInvoices = $query1->get();

            $leaseIdsAll = [];
            foreach ($lateInvoices as $i) {
                $leaseIdsAll[]= $i->lease_id;
            }
            $leaseIds = array_unique($leaseIdsAll);

            $lateLeases = Lease::whereIn('id',$leaseIds)->get();

            $lateInfo = '';
            foreach ($lateLeases as $lease) {
                //echo "User: " . $user->id . " Unit: " . $lease->unit_id . " Lease: " . $lease->id . " Due Date: " . $lease->monthly_due_date."\n";
                $lateInfo .= ucwords($lease->full_name)
                            . ' - ' . $lease->email
                            . ', ' . $lease->unit->property->full_address
                            . ', ' . $lease->unit->name
                            . ', ' . \Carbon\Carbon::now()->addDay()->format('m/d/Y') . '<br>';
            }
            if (count($lateLeases) != 0) {
                $user->notify(new ListOfTenantsWhoDidntPay($lateInfo));
            }

        }
    }
}
