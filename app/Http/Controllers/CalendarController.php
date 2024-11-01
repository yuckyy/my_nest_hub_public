<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index(Request $request){

        if($request->ajax()) {

            $data = Event::where('user_id', Auth::user()->id)
                ->whereDate('start', '>=', $request->start)
                ->whereDate('end',   '<=', $request->end)
                ->get(['id', 'title', 'start', 'end']);
            return response()->json($data);
        }
        return view('calendar.index');
    }

    public function events(Request $request){

        $query1 = DB::table('invoices');
        //$query1->leftJoin(
        //    DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
        //    'invoices.id', '=', 'p.invoice_id'
        //);
        //invoice based on lease
        $query1->join('leases', 'leases.id', '=', 'invoices.base_id');
        $query1->join('units', 'units.id', '=', 'leases.unit_id');
        $query1->join('properties', 'properties.id', '=', 'units.property_id');
        $query1->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query1->where('is_lease_pay', '=', '1');
        $query1->where('properties.user_id', '=', Auth::user()->id);
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query1->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query1->where('leases.email', '=', Auth::user()->email);
        }

        $query1->whereDate('invoices.due_date', '>=', $request->start);
        $query1->whereDate('invoices.due_date', '<=', $request->end);
        $query1->select(
            DB::raw('CONCAT("invoice-", invoices.id) AS id'),
            DB::raw('CONCAT("$", invoices.amount, " invoice for ", properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS title'),
            DB::raw('invoices.due_date AS start'),
            DB::raw('invoices.due_date AS end'),
            DB::raw('0 AS editable'),
            DB::raw('"#5683cc" AS color'),
            DB::raw('1 AS allDay'),
            DB::raw('invoices.id AS invoice_id'),
            DB::raw('leases.id AS lease_id'),
            DB::raw('0 AS due_date')
        );

        $query = DB::table('invoices');
        //$query->leftJoin(
        //    DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
        //    'invoices.id', '=', 'p.invoice_id'
        //);
        // invoice based on bill
        $query->join('bills', 'bills.id', '=', 'invoices.base_id');
        $query->join('leases', 'leases.id', '=', 'bills.lease_id');
        $query->join('units', 'units.id', '=', 'leases.unit_id');
        $query->join('properties', 'properties.id', '=', 'units.property_id');
        $query->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query->where('is_lease_pay', '=', '0');

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query->where('leases.email', '=', Auth::user()->email);
        }

        $query->whereDate('invoices.due_date', '>=', $request->start);
        $query->whereDate('invoices.due_date',   '<=', $request->end);
        $query->select(
            DB::raw('CONCAT("invoice-", invoices.id) AS id'),
            DB::raw('CONCAT("$", invoices.amount, " invoice for ", properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS title'),
            //DB::raw('CONCAT(leases.firstname, " ", leases.lastname, " ", invoices.description) AS description'),
            DB::raw('invoices.due_date AS start'),
            DB::raw('invoices.due_date AS end'),
            DB::raw('0 AS editable'),
            DB::raw('"#5683cc" AS color'),
            DB::raw('1 AS allDay'),
            DB::raw('invoices.id AS invoice_id'),
            DB::raw('leases.id AS lease_id'),
            DB::raw('0 AS due_date')
        );


        //events
        $query2 = DB::table('events');
        $query2->where('user_id', Auth::user()->id)
            ->whereDate('start', '>=', $request->start)
            ->whereDate('end',   '<=', $request->end)
            ->select(
                'id',
                'title',
                'start',
                'end',
                DB::raw('1 AS editable'),
                DB::raw('"#d9534f" AS color'),
                DB::raw('1 AS allDay'),
                DB::raw('0 AS invoice_id'),
                DB::raw('0 AS lease_id'),
                DB::raw('0 AS due_date')
            );

        //leases due date
        $query3 = DB::table('leases');
        $query3->join('units', 'units.id', '=', 'leases.unit_id');
        $query3->join('properties', 'properties.id', '=', 'units.property_id');
        $query3->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query3->where('properties.user_id', '=', Auth::user()->id);
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query3->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query3->where('leases.email', '=', Auth::user()->email);
        }

        //$query3->whereDate('leases.due_date', '>=', $request->start);
        $query3->where(function ($q) use ($request) {
            $q->whereDate('leases.end_date', '>=', $request->start)->orWhereNull("leases.end_date");
        });
        $query3->whereDate('leases.start_date',   '<=', $request->end);
        $query3->whereNull('leases.deleted_at');
        $query3->select(
            DB::raw('CONCAT("lease-", leases.id) AS id'),
            DB::raw('CONCAT("Lease for ", properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS title'),
            DB::raw('"" AS start'),
            DB::raw('"" AS end'),
            DB::raw('0 AS editable'),
            DB::raw('"#999999" AS color'),
            DB::raw('1 AS allDay'),
            DB::raw('0 AS invoice_id'),
            DB::raw('leases.id AS lease_id'),
            DB::raw('leases.monthly_due_date AS due_date'),

            DB::raw('leases.start_date AS lease_start_date')
        );


        /**/

        $query->union($query1);
        $data = $query->get();

        $dataOutput = [];
        $leaseHash = [];
        foreach($data as $key => $event) {
            if($data[$key]->invoice_id != 0) {
                $leaseHash[] = $data[$key]->lease_id . '-' . Carbon::parse($data[$key]->start)->format('Y-m-d');
            }
            $data[$key]->extendedProps = [];
            $invoice_id = $data[$key]->invoice_id;
            $data[$key]->extendedProps += ['invoice_id'=>$invoice_id];
            $lease_id = $data[$key]->lease_id;
            $data[$key]->extendedProps += ['lease_id'=>$lease_id];
            $dataOutput[] = clone $data[$key];
        }

        $data2 = $query2->get();
        foreach($data2 as $key => $event) {
            $dataOutput[] = clone $data2[$key];
        }

        $data3 = $query3->get();
        foreach($data3 as $key => $event){
            $data3[$key]->extendedProps = [];

            //$invoice_id = $data3[$key]->invoice_id;
            //$data3[$key]->extendedProps += ['invoice_id'=>$invoice_id];
            $lease_id = $data3[$key]->lease_id;
            $data3[$key]->extendedProps += ['lease_id'=>$lease_id];

            for($m=0;$m<=2;$m++) {
                if($m == 0){
                    $leasePaymentDate = Carbon::parse($request->start)->startOfMonth()->addDays($data3[$key]->due_date - 1)->format('Y-m-d');
                } else {
                    $leasePaymentDate = Carbon::parse($request->start)->startOfMonth()->addMonth($m)->addDays($data3[$key]->due_date - 1)->format('Y-m-d');
                }
                if(
                    (Carbon::parse($leasePaymentDate) >= Carbon::parse($request->start))
                    &&
                    (Carbon::parse($leasePaymentDate) <= Carbon::parse($request->end))
                    &&
                    (Carbon::parse($leasePaymentDate) >= Carbon::parse($data3[$key]->lease_start_date))
                ){
                    if($data3[$key]->start == ""){
                        $data3[$key]->start = $leasePaymentDate;
                        $data3[$key]->end = $leasePaymentDate;
                    } else {
                        $addData2 = clone $data3[$key];
                        $addData2->start = $leasePaymentDate;
                        $addData2->end = $leasePaymentDate;

                        $leaseHashCheck = $lease_id . '-' . Carbon::parse($addData2->start)->format('Y-m-d');
                        if(!in_array($leaseHashCheck, $leaseHash)){
                            $dataOutput[] = $addData2;
                        }
                    }
                }
            }

            $leaseHashCheck = $lease_id . '-' . Carbon::parse($data3[$key]->start)->format('Y-m-d');
            if(!in_array($leaseHashCheck, $leaseHash)){
                $dataOutput[] = clone $data3[$key];
            }
        }
        return response()->json($dataOutput);
    }

    public function post(Request $request){
        switch ($request->type) {
            case 'add':
                $event = Event::create([
                    'user_id' => Auth::user()->id,
                    'title' => $request->title,
                    'description' => $request->description ?? "",
                    'start' => $request->start,
                    'end' => $request->end,
                ]);

                return response()->json($event);
                break;

            case 'drop':
                $event = Event::where('id',$request->id)
                    ->where('user_id', Auth::user()->id)
                    ->first()
                    ->update([
                        'start' => $request->start,
                        'end' => $request->end,
                    ]);
                return response()->json($event);
                break;

            case 'update':
                $event = Event::where('id',$request->id)
                    ->where('user_id', Auth::user()->id)
                    ->first()
                    ->update([
                        'title' => $request->title,
                        'start' => $request->start,
                        'end' => $request->end,
                    ]);
                return response()->json($event);
                break;

            case 'delete':
                $event = Event::where('id',$request->id)
                    ->where('user_id', Auth::user()->id)
                    ->first()
                    ->delete();
                return response()->json($event);
                break;

            default:
                # code...
                break;
        }

    }

    public function details(Request $request){
        $event_start = $request->event_start;
        if(!empty($request->invoice_id)){
            $invoice = Invoice::find($request->invoice_id);
            return view('calendar.ajax-invoice-details',compact('invoice'));
        } else if(!empty($request->lease_id)){
            $lease = Lease::find($request->lease_id);
            return view('calendar.ajax-lease-details',compact('lease', 'event_start'));
        } else {
            $event = Event::find($request->event_id);
            return view('calendar.ajax-details',compact('event'));
        }
    }

}
