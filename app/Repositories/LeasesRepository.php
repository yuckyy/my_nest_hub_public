<?php

namespace App\Repositories;

use App\Models\Application;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use \Carbon\Carbon;

class LeasesRepository implements LeasesRepositoryInterface {

    public function get(array $data = []) {

        return Lease::paginate(config('app.per_page'));
    }

    public function getAll() {
        return Lease::all();
    }

    public function getById(int $id) {
        return Lease::find($id);
    }

    public function removeInvoices(Lease $l)
    {
        //remove all 'generated' invoices
        $invoicesToRemove0 = Invoice::where([['base_id',$l->id],['is_lease_pay',1]])->get();
        foreach ($invoicesToRemove0 as $i) {
            $p = Payment::where([['invoice_id', $i->id],['payment_method','Generated']])->first();
            if(!empty($p)){
                $p->delete();
                $i->delete();
            }
        }

        //find latest paid bill
        $latestPaid = Invoice::where([['base_id',$l->id],['is_lease_pay',1]])
            ->get()
            ->filter(function($item) {
                if ($item->payed > 0) {
                    return $item;
                }
            })->sortBy('pay_month')->first();
        if(!empty($latestPaid)) {
            //remove invoices after the paid
            $invoicesToRemove2 = Invoice::where([['base_id', $l->id], ['is_lease_pay', 1]])
                ->whereDate('pay_month', '>', $latestPaid->pay_month)
                ->get()
                ->filter(function ($item) {
                    if ($item->payed == 0) {
                        return $item;
                    }
                });
            foreach ($invoicesToRemove2 as $i) {
                $i->delete();
            }
        } else {
            //remove all invoices
            $invoicesToRemove1 = Invoice::where([['base_id',$l->id],['is_lease_pay',1]])
                ->get()
                ->filter(function($item) {
                    if ($item->payed == 0) {
                        return $item;
                    }
                });
            foreach ($invoicesToRemove1 as $i) {
                $i->delete();
            }
        }


        /*
        $invoicesToRemove1 = Invoice::where([['base_id',$l->id],['is_lease_pay',1]])
                    ->whereDate('pay_month', '<' ,$l->start_date)
                    ->get()
                    ->filter(function($item) {
                        if ($item->payed == 0) {
                            return $item;
                        }
                    });
        foreach ($invoicesToRemove1 as $i) {
            $i->delete();
        }
        if ($l->end_date) {
            $invoicesToRemove2 = Invoice::where([['base_id',$l->id],['is_lease_pay',1]])
                        ->whereDate('pay_month', '>' ,$l->end_date)
                        ->get()
                        ->filter(function($item) {
                            if ($item->payed == 0) {
                                return $item;
                            }
                        });
            foreach ($invoicesToRemove2 as $i) {
                $i->delete();
            }
        }
        */
    }

    public function getDatesArray(Lease $l)
    {
        // calculate dates for invoices
        $start = Carbon::parse($l->start_date);
        if($l->end_date && $l->end_date < Carbon::today()){
            $end = Carbon::parse($l->end_date);
            $diff = $end->diffInMonths($start);
        } else {
            $end = Carbon::today();
            $diff = $end->diffInMonths($start) + 1;
        }
        $due = $l->monthly_due_date;

        if ($start->format('d') <= $due) {
            $due_date = Carbon::create($start->format('Y'),$start->format('m'),$due,00,00,00)->format('Y-m-d');
        } else {
            $due_date = $start->format('Y-m-d');
            //$due_date = Carbon::create($start->format('Y'),$start->format('m')+1,$due,00,00,00)->format('Y-m-d');
        }

        // keep it. may be it's better to set index by the first day of month
        //$firstDayOfNextMonth = $start->startOfMonth()->addMonthsNoOverflow()->toDateString();

        $due_dates = [
            Carbon::parse($l->start_date)->format('Y-m-d') => $due_date
        ];
        for ($i=1; $i <= $diff; $i++) {
            $date = Carbon::parse($l->start_date)->addMonthsNoOverflow($i);
            $due_dates [
                $date->format('Y-m-d')
            ] = Carbon::create($date->format('Y'),$date->format('m'),$due,00,00,00)->format('Y-m-d');
        }

        /*
        if ($due - 7 <= Carbon::today()->format('d')) {
            $due_dates [
                Carbon::create(Carbon::today()->format('Y'),Carbon::today()->format('m'),Carbon::parse($l->start_date)->format('d'),00,00,00)->format('Y-m-d')
            ] = Carbon::create(Carbon::today()->format('Y'),Carbon::today()->format('m'),$due,00,00,00)->format('Y-m-d');
        }
        */
        return $due_dates;
    }

    public function createInvoices(Lease $l)
    {
        $this->removeInvoices($l);

        $due_dates = $this->getDatesArray($l);
        foreach ($due_dates as $pmonth => $d) {
            // create invoices for every month
            if (!Invoice::where([
                        ['base_id',$l->id],
                        ['is_lease_pay',1],
                        ['is_late_fee',0,]
                    ])
                    ->whereYear('pay_month', '=', explode('-',$pmonth)[0])
                    ->whereMonth('pay_month', '=', explode('-',$pmonth)[1])
                    ->first()
                ) {

                $invoice = Invoice::create([
                    'base_id' => $l->id,
                    'is_lease_pay' => 1,
                    'is_late_fee' => 0,
                    'due_date' => $d,
                    'pay_month' => $pmonth,
                    'description' => 'Monthly Rent',
                    'amount' => $l->total_by_tenant,
                ]);

                //mark as paid for old invoices
                if(Carbon::parse($d)->timestamp < Carbon::now()->subDays(30)->timestamp){
                    Payment::create([
                        'pay_date' => $d,
                        'amount' => $l->total_by_tenant,
                        'invoice_id' => $invoice->id,
                        'payment_method' => 'Generated',
                    ]);
                }
            }
        }
        //  remove invoices if pay_month are out of updated start/end dates range and is not payed
        //$this->removeInvoices($l);
    }

    /*
    public function createNextInvoice(Lease $l)
    {
        // same as "getDatesArray()" but without end date
        $start = Carbon::parse($l->start_date);
        $end = Carbon::today();
        $diff = $end->diffInMonths($start) + 1;
        $due = $l->monthly_due_date;

        if ($start->format('d') <= $due) {
            $due_date = Carbon::create($start->format('Y'),$start->format('m'),$due,00,00,00)->format('Y-m-d');
        } else {
            $due_date = $start->format('Y-m-d');
        }

        $due_dates = [
            Carbon::parse($l->start_date)->format('Y-m-d') => $due_date
        ];
        for ($i=1; $i <= $diff; $i++) {
            $date = Carbon::parse($l->start_date)->addMonthsNoOverflow($i);
            $due_dates [
                $date->format('Y-m-d')
            ] = Carbon::create($date->format('Y'),$date->format('m'),$due,00,00,00)->format('Y-m-d');
        }

        foreach ($due_dates as $pmonth => $d) {
            //echo "\n---".$d.'<---'.$pmonth."\n";
            // create invoices for every month
            if (!Invoice::where([
                ['base_id',$l->id],
                ['is_lease_pay',1],
                ['is_late_fee',0,]
            ])
                ->whereYear('pay_month', '=', explode('-',$pmonth)[0])
                ->whereMonth('pay_month', '=', explode('-',$pmonth)[1])
                ->first()
            ) {
                Invoice::create([
                    'base_id' => $l->id,
                    'is_lease_pay' => 1,
                    'is_late_fee' => 0,
                    'due_date' => $d,
                    'pay_month' => $pmonth,
                    'description' => 'Monthly Rent',
                    'amount' => $l->total_by_tenant,
                ]);
            }
        }
    }
    */

    public function createLateFeeInvoice(Lease $l)
    {
        if (!Invoice::where([
                    ['base_id',$l->id],
                    ['is_lease_pay',1],
                    ['is_late_fee',1]
                ])->whereDate('pay_month',Carbon::today())->first()
            ) {
            Invoice::create([
                'base_id' => $l->id,
                'is_lease_pay' => 1,
                'is_late_fee' => 1,
                'due_date' => Carbon::now()->addDay(),
                'pay_month' => Carbon::today(),
                'description' => 'Late Fee',
                'amount' => $l->late_fee_amount,
            ]);
        }
    }
}
