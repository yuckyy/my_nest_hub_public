<?php

namespace App\Console\Commands;

use App\Models\FinanceUnit;
use App\Models\Invoice;
use Illuminate\Console\Command;

use Carbon\Carbon;
use App\Models\Lease;
use App\Models\User;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use App\Notifications\RentIsLate;
use App\Notifications\RentIsLateNotificationDatabase;
use App\Notifications\TenantRentIsDueSoonNotification;
use App\Notifications\TenantRentIsDueSoonNotificationDatabase;
use App\Notifications\LandlordLateFeeNotificationDatabase;
use App\Notifications\LandlordLateFee;
use Illuminate\Support\Facades\DB;

class CreateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $lr;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LeasesRepositoryInterface $lr)
    {
        parent::__construct();

        $this->lr = $lr;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get all leases with monthly_due_date = today + 7 days date
        $process_due_date = Carbon::now()->addDays(7);

        //for debug. custom date
        //$process_due_date = Carbon::parse("2021-09-03");

        $leases = Lease::where('monthly_due_date',$process_due_date->format('d'))->whereNull('deleted_at')->get();

        foreach ($leases as $l) {
           // echo $l->email;

            $start = Carbon::parse($l->start_date);
            $start2 = Carbon::create($start->format('Y'),$start->format('m'),2,00,00,00);
            $end2 = Carbon::create($process_due_date->format('Y'),$process_due_date->format('m'),2,00,00,00);
            $diff = $end2->diffInMonths($start2);

            if ($start->format('d') <= $l->monthly_due_date) {
                $due_date = Carbon::create($start->format('Y'),$start->format('m'),$l->monthly_due_date,00,00,00)->format('Y-m-d');
            } else {
                $due_date = $start->format('Y-m-d');
            }

            // $due_dates[Payment Month] = Due Date for Payment Month
            $due_dates = [
                Carbon::parse($l->start_date)->format('Y-m-1') => $due_date
            ];
            for ($i=1; $i <= $diff; $i++) {
                $date = Carbon::parse($l->start_date)->addMonthsNoOverflow($i);
                $due_dates [
                    $date->format('Y-m-1')
                ] = Carbon::create($date->format('Y'),$date->format('m'),$l->monthly_due_date,00,00,00)->format('Y-m-d');
            }

            foreach ($due_dates as $payment_month => $d) {
                // create invoices for every month if not exists
                if (!Invoice::where([
                    ['base_id',$l->id],
                    ['is_lease_pay',1],
                    ['is_late_fee',0,]
                ])
                    ->whereYear('pay_month', '=', explode('-',$payment_month)[0])
                    ->whereMonth('pay_month', '=', explode('-',$payment_month)[1])
                    ->first()
                ) {
                    Invoice::create([
                        'base_id' => $l->id,
                        'is_lease_pay' => 1,
                        'is_late_fee' => 0,
                        'due_date' => $d,
                        'pay_month' => $payment_month,
                        'description' => 'Monthly Rent',
                        'amount' => $l->total_by_tenant,
                    ]);
                }
            }

            $tenant = User::where('email',$l->email)->first();

            //TODO send only if negative amount
            //if( $l->invoices()->sum('amount') - $l->deposit > 0 ){

            //Don't send regulat email if user didn't registered
            if(!empty($tenant)) {
                //print "Urer:".$tenant->id;
                $tenant->notify(new TenantRentIsDueSoonNotification());
                $tenant->notify(new TenantRentIsDueSoonNotificationDatabase($l));
            }
        }

        //tenant's selected payment day
        $tenant_selected_payment_day = $process_due_date->format('d');
        $fUnits = FinanceUnit::where('recurring_payment_day',$tenant_selected_payment_day)->get();
        foreach ($fUnits as $fu) {
            $leases = Lease::where('unit_id', $fu->unit_id)->where('email', $fu->user->email)->whereNull('deleted_at')->get();
            foreach ($leases as $l) {
                //echo $l->email;
                if($tenant_selected_payment_day > $l->monthly_due_date){
                    //create invoice for the next month
                    $plus_one_month = clone $process_due_date;
                    $plus_one_month->addMonthsNoOverflow(1);
                    $payment_month = $plus_one_month->format('Y-m-1');
                    $d = Carbon::create($plus_one_month->format('Y'),$plus_one_month->format('m'),$l->monthly_due_date,00,00,00)->format('Y-m-d');
                    if (!Invoice::where([
                        ['base_id',$l->id],
                        ['is_lease_pay',1],
                        ['is_late_fee',0,]
                    ])
                        ->whereYear('pay_month', '=', explode('-',$payment_month)[0])
                        ->whereMonth('pay_month', '=', explode('-',$payment_month)[1])
                        ->first()
                    ) {
                        Invoice::create([
                            'base_id' => $l->id,
                            'is_lease_pay' => 1,
                            'is_late_fee' => 0,
                            'due_date' => $d,
                            'pay_month' => $payment_month,
                            'description' => 'Monthly Rent',
                            'amount' => $l->total_by_tenant,
                        ]);
                    }
                }
            }
        }


        // get all late leases having late fee
        //TODO change it with the db query as done below. remove LateLeases from the model
        $lateFees = Lease::lateLeases();
        foreach ($lateFees as $lf) {
            \Log::info("Cron: late fee. lease ID: " . $lf->id . ",email: " . $lf->email);
            //echo $lf->landlord->email;
        }

        foreach ($lateFees as $lf) {
            // create new invoice for this Late Fee
            $this->lr->createLateFeeInvoice($lf);
            $lf->notify(new RentIsLate($lf->unit->property, $lf->unit));

            $tenant = User::where('email',$lf->email)->first();
            if(!empty($tenant)) {
                $tenant->notify(new RentIsLateNotificationDatabase($lf));
            }
            $landlord = $lf->unit->property->user;
            $landlord->notify(new LandlordLateFeeNotificationDatabase($lf));
        }

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
            //$query1->where('leases.monthly_due_date', '=', Carbon::now()->addDay()->format('d'));
            $query1->select(
                'leases.id as lease_id'
            );
            $lateInvoices = $query1->get();

            $leaseIdsAll = [];
            foreach ($lateInvoices as $i) {
                $leaseIdsAll[]= $i->lease_id;
            }
            $leaseIds = array_unique($leaseIdsAll);

            $lateLeases = Lease::where('late_fee_amount','>',0)
                ->whereRaw('DAY(DATE_SUB(NOW(), INTERVAL `late_fee_day` DAY)) = monthly_due_date')
                ->whereIn('id',$leaseIds)
                ->get();

            //$lateLeases = Lease::lateLeases($user->id);

            $lateInfo = '';
            foreach ($lateLeases as $lease) {
                $lateInfo .= ucwords($lease->full_name)
                            . ' - ' . $lease->email
                            . ', ' . $lease->unit->property->full_address
                            . ', ' . $lease->unit->name . '<br>';
            }
            if (count($lateLeases) != 0) {
                //echo $user->email;
                $user->notify(new LandlordLateFee($lateInfo));
            }
        }

        \Log::info("Cron: CreateInvoices");
    }
}
