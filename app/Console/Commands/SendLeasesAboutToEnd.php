<?php

namespace App\Console\Commands;
use App\Models\User;
use App\Models\Lease;
use App\Notifications\LeaseIsAboutToEnd;
use App\Notifications\LeaseIsAboutToEndNotificationDatabase;
use App\Notifications\LeaseOfTenantEnded;
use App\Notifications\LeaseOfTenantEndedNotificationDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SendLeasesAboutToEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:leases-end';

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
        //About to end
        $query = DB::table('leases');
        $query->join('units', 'units.id', '=', 'leases.unit_id');
        $query->join('properties', 'properties.id', '=', 'units.property_id');
        //$query->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query->whereNull('leases.deleted_at');
        $query->whereDate('leases.end_date', '=', \Carbon\Carbon::now()->addDays(10));
        $query->select(
            DB::raw('properties.user_id AS user_id'),
            DB::raw('leases.id AS lease_id')
            //DB::raw('leases.email AS email'),
            //DB::raw('units.name AS unit_name'),
            //DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_address'),
            //DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_name')
        );
        $leasesData = $query->get();

        $leasesByUser = [];
        foreach($leasesData as $d) {
            if(empty($leasesByUser[$d->user_id])){
                $leasesByUser[$d->user_id] = [];
            }
            $leasesByUser[$d->user_id][] = $d->lease_id;
        }

        foreach($leasesByUser as $userId => $leaseIds) {
            $user = User::find($userId);
            $tenantToEndInfo = '';
            foreach($leaseIds as $leaseId) {
                $lease = Lease::find($leaseId);
                //print "User:".$user->id.'. Lease:'.$leaseId . PHP_EOL;
                $tenantToEndInfo .= ucwords($lease->full_name)
                    . ' - ' . $lease->email
                    . ', ' . $lease->unit->property->full_address
                    . ', ' . $lease->unit->name . '<br>';
                $user->notify(new LeaseIsAboutToEndNotificationDatabase($lease));
            }
            if (count($leaseIds) != 0) {
                //print "User:".$user->id. PHP_EOL;
                $user->notify(new LeaseIsAboutToEnd($tenantToEndInfo));
            }
        }

        //Ended
        $query = DB::table('leases');
        $query->join('units', 'units.id', '=', 'leases.unit_id');
        $query->join('properties', 'properties.id', '=', 'units.property_id');
        $query->whereNull('leases.deleted_at');
        $query->whereDate('leases.end_date', '=', \Carbon\Carbon::now());
        $query->select(
            DB::raw('properties.user_id AS user_id'),
            DB::raw('leases.id AS lease_id')
        );
        $leasesData = $query->get();

        $leasesByUser = [];
        foreach($leasesData as $d) {
            if(empty($leasesByUser[$d->user_id])){
                $leasesByUser[$d->user_id] = [];
            }
            $leasesByUser[$d->user_id][] = $d->lease_id;
        }

        foreach($leasesByUser as $userId => $leaseIds) {
            $user = User::find($userId);
            $tenantToEndInfo = '';
            foreach($leaseIds as $leaseId) {
                $lease = Lease::find($leaseId);
                //print "User:".$user->id.'. Lease:'.$leaseId . PHP_EOL;
                $tenantToEndInfo .= ucwords($lease->full_name)
                    . ' - ' . $lease->email
                    . ', ' . $lease->unit->property->full_address
                    . ', ' . $lease->unit->name . '<br>';
                $user->notify(new LeaseOfTenantEndedNotificationDatabase($lease));
            }
            if (count($leaseIds) != 0) {
                //print "User:".$user->id. PHP_EOL;
                $user->notify(new LeaseOfTenantEnded($tenantToEndInfo));
            }
        }

        return;
    }
}
