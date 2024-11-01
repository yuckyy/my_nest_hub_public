<?php

namespace App\Http\Controllers\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Payment;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index(Request $request) {

        /* for tenant */
        if (Auth::user()->isTenant()) {
            $user = Auth::user();

            $activeLeases = Lease::where('email', $user->email)->get();
            $inactiveLeases = Lease::onlyTrashed()->where('email', $user->email)->get();

            if($request['lease']){
                $selectedLease = Lease::withTrashed()
                    ->where('id', $request['lease'])
                    ->first();
            } else {
                $selectedLease = $activeLeases->first();
                if(!$selectedLease){
                    $selectedLease = $user->leases->first();
                }
            }

            return view(
                'tenant.payments.index',
                [
                    'user' => $user,
                    'selectedLease' => $selectedLease ?? false,
                    'activeLeases' => $activeLeases ?? false,
                    'inactiveLeases' => $inactiveLeases ?? false,
                ]
            );
        }

        /* for landlord */
        $query1 = DB::table('payments');
        $query1->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id');
        //invoice based on lease
        $query1->join('leases', 'leases.id', '=', 'invoices.base_id');
        $query1->join('units', 'units.id', '=', 'leases.unit_id');
        $query1->join('properties', 'properties.id', '=', 'units.property_id');
        $query1->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query1->where('is_lease_pay', '=', '1');

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            //keep this for future compatibility
            $query1->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query1->where('leases.email', '=', Auth::user()->email);
        }

        //Filter
        if (!empty($request['property_id_unit_id'])) {
            $property_id_unit_id = explode('_',$request['property_id_unit_id']);
            if( (float)$property_id_unit_id[1] > 0 ){
                $query1->where('units.id', $property_id_unit_id[1]);
            } else {
                $query1->where('properties.id', $property_id_unit_id[0]);
            }
        }
        if (!empty($request['search'])) {
            $query1->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('states.code', '=', \Request::get('search'))
                    ->orWhere('properties.zip', '=', \Request::get('search'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.email', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.firstname', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.lastname', 'like', '%' . \Request::get('search') . '%');
            });
        }
        //advanced filter
        if (!empty($request['pay_date'])) {
            $query1->where('payments.pay_date', '=', $request['pay_date']);
        }
        if (!empty($request['amount'])) {
            $query1->where('payments.amount', '=', $request['amount']);
        }
        if (!empty($request['full_user'])) {
            $query1->where(function($q) {
                $q->where('leases.firstname', 'like', '%' . \Request::get('full_user') . '%')
                    ->orWhere('leases.lastname', 'like', '%' . \Request::get('full_user') . '%');
            });
        }
        if (!empty($request['email'])) {
            $query1->where('leases.email', 'like', '%' . $request['email'] . '%');
        }
        if (!empty($request['property_unit'])) {
            $query1->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('states.code', '=', \Request::get('property_unit'))
                    ->orWhere('properties.zip', '=', \Request::get('property_unit'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('units.name', 'like', '%' . \Request::get('property_unit') . '%');
            });
        }
        if (!empty($request['description'])) {
            $query1->where('invoices.description', 'like', '%' . $request['description'] . '%');
        }
        //--

        $query1->select(
            'leases.email',
            DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
            'invoices.due_date',
            'invoices.description',
            'payments.pay_date',
            DB::raw('payments.id AS payment_id'),
            DB::raw('invoices.id AS invoice_id'),
            'payments.amount',
            'leases.unit_id',
            DB::raw('leases.id AS lease_id'),
            DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit')
        );

        $query = DB::table('payments');
        $query->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id');
        // invoice based on bill
        $query->join('bills', 'bills.id', '=', 'invoices.base_id');
        $query->join('leases', 'leases.id', '=', 'bills.lease_id');
        $query->join('units', 'units.id', '=', 'leases.unit_id');
        $query->join('properties', 'properties.id', '=', 'units.property_id');
        $query->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query->where('is_lease_pay', '=', '0');

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            //keep this for future compatibility
            $query->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query->where('leases.email', '=', Auth::user()->email);
        }

        //Filter
        if (!empty($request['property_id_unit_id'])) {
            $property_id_unit_id = explode('_',$request['property_id_unit_id']);
            if( (float)$property_id_unit_id[1] > 0 ){
                $query->where('units.id', $property_id_unit_id[1]);
            } else {
                $query->where('properties.id', $property_id_unit_id[0]);
            }
        }
        if (!empty($request['search'])) {
            $query->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('states.code', '=', \Request::get('search'))
                    ->orWhere('properties.zip', '=', \Request::get('search'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.email', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.firstname', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.lastname', 'like', '%' . \Request::get('search') . '%');
            });
        }
        //advanced filter
        if (!empty($request['pay_date'])) {
            $query->where('payments.pay_date', '=', $request['pay_date']);
        }
        if (!empty($request['amount'])) {
            $query->where('payments.amount', '=', $request['amount']);
        }
        if (!empty($request['full_user'])) {
            $query->where(function($q) {
                $q->where('leases.firstname', 'like', '%' . \Request::get('full_user') . '%')
                  ->orWhere('leases.lastname', 'like', '%' . \Request::get('full_user') . '%');
            });
        }
        if (!empty($request['email'])) {
            $query->where('leases.email', 'like', '%' . $request['email'] . '%');
        }
        if (!empty($request['property_unit'])) {
            $query->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('states.code', '=', \Request::get('property_unit'))
                    ->orWhere('properties.zip', '=', \Request::get('property_unit'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('units.name', 'like', '%' . \Request::get('property_unit') . '%');
            });
        }
        if (!empty($request['description'])) {
            $query->where('invoices.description', 'like', '%' . $request['description'] . '%');
        }
        //--

        $query->select(
            'leases.email',
            DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
            'invoices.due_date',
            'invoices.description',
            'payments.pay_date',
            DB::raw('payments.id AS payment_id'),
            DB::raw('invoices.id AS invoice_id'),
            'payments.amount',
            'leases.unit_id',
            DB::raw('leases.id AS lease_id'),
            DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit')
        );

        $query->union($query1);

        //Sorting
        $availableColumns = [
            'full_user',
            'email',
            'property_unit',
            'pay_date',
            'description',
            'amount'
        ];
        $column = $request->get('column');
        if (in_array($column, $availableColumns)) {
            if ($request->get('order') === 'asc') {
                $query->orderBy($column, 'asc');
            } else {
                $query->orderBy($column, 'desc');
            }
        } else {
            $query->orderBy('pay_date', 'desc');
        }
        $payments = $query->paginate(50);

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $properties_query = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, 0 AS unit_id, "" AS unit_name'));
            $properties_units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->union($properties_query)
                ->orderBy('property_id', 'asc')
                ->orderBy('unit_id', 'asc')
                ->get();

            $units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->orderBy('properties.id', 'asc')
                ->orderBy('units.id', 'asc')
                ->select(['units.*', 'units.id AS unit_id'])
                ->get();
        } else {
            $properties_units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->join('properties', 'properties.id', '=', 'units.property_id')
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->get();

            $units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT units.*'))
                ->get();
        }

        return view(
            'payments.index',
            [
                'payments' => $payments,
                'units' => $units,
                'properties_units' => $properties_units,
                'advanced_filter' => $request['advanced_filter'] ?? "0",
            ]
        );
    }

    public function invoices(Request $request) {

        /* for tenant */
        if (Auth::user()->isTenant()) {
            return true;
        }

        /* for landlord */
        $query1 = DB::table('invoices');
        $query1->leftJoin(
            DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
            'invoices.id', '=', 'p.invoice_id'
        );
        //invoice based on lease
        $query1->join('leases', 'leases.id', '=', 'invoices.base_id');
        $query1->join('units', 'units.id', '=', 'leases.unit_id');
        $query1->join('properties', 'properties.id', '=', 'units.property_id');
        $query1->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query1->where('is_lease_pay', '=', '1');
        $query1->where('properties.user_id', '=', Auth::user()->id);
        //$query1->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id');
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            //keep this for future compatibility
            $query1->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query1->where('leases.email', '=', Auth::user()->email);
        }

        //Filter
        if (!empty($request['unpaid'])) {
            $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
        }
        if (!empty($request['paid'])) {
            $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount = 0');
        }
        if (!empty($request['upcoming'])) {
            $query1->whereRaw('invoices.due_date >= NOW()');
        }
        if (!empty($request['property_id_unit_id'])) {
            $property_id_unit_id = explode('_',$request['property_id_unit_id']);
            if( (float)$property_id_unit_id[1] > 0 ){
                $query1->where('units.id', $property_id_unit_id[1]);
            } else {
                $query1->where('properties.id', $property_id_unit_id[0]);
            }
        }
        if (!empty($request['search'])) {
            $query1->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('states.code', '=', \Request::get('search'))
                    ->orWhere('properties.zip', '=', \Request::get('search'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.email', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.firstname', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.lastname', 'like', '%' . \Request::get('search') . '%');
            });
        }
        //advanced filter
        if (!empty($request['due_date'])) {
            $query1->where('invoices.due_date', '=', $request['due_date']);
        }
        if (!empty($request['balance'])) {
            $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount = ?', [$request['balance']]);
        }
        if (!empty($request['full_user'])) {
            $query1->where(function($q) {
                $q->where('leases.firstname', 'like', '%' . \Request::get('full_user') . '%')
                    ->orWhere('leases.lastname', 'like', '%' . \Request::get('full_user') . '%');
            });
        }
        if (!empty($request['email'])) {
            $query1->where('leases.email', 'like', '%' . $request['email'] . '%');
        }
        if (!empty($request['property_unit'])) {
            $query1->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('states.code', '=', \Request::get('property_unit'))
                    ->orWhere('properties.zip', '=', \Request::get('property_unit'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('units.name', 'like', '%' . \Request::get('property_unit') . '%');
            });
        }
        if (!empty($request['description'])) {
            $query1->where('invoices.description', 'like', '%' . $request['description'] . '%');
        }
        //--

        $query1->select(
            'leases.email',
            DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
            'invoices.due_date',
            'invoices.description',
            DB::raw('invoices.id AS invoice_id'),
            'leases.unit_id',
            DB::raw('leases.id AS lease_id'),
            DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
            DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit'),
            DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance')
        );

        $query = DB::table('invoices');
        $query->leftJoin(
            DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
            'invoices.id', '=', 'p.invoice_id'
        );
        // invoice based on bill
        $query->join('bills', 'bills.id', '=', 'invoices.base_id');
        $query->join('leases', 'leases.id', '=', 'bills.lease_id');
        $query->join('units', 'units.id', '=', 'leases.unit_id');
        $query->join('properties', 'properties.id', '=', 'units.property_id');
        $query->leftJoin('states', 'states.id', '=', 'properties.state_id');
        $query->where('is_lease_pay', '=', '0');

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            //keep this for future compatibility
            $query->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query->where('leases.email', '=', Auth::user()->email);
        }

        //Filter
        if (!empty($request['unpaid'])) {
            $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
        }
        if (!empty($request['paid'])) {
            $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount = 0');
        }
        if (!empty($request['upcoming'])) {
            $query->whereRaw('invoices.due_date >= NOW()');
        }
        if (!empty($request['property_id_unit_id'])) {
            $property_id_unit_id = explode('_',$request['property_id_unit_id']);
            if( (float)$property_id_unit_id[1] > 0 ){
                $query->where('units.id', $property_id_unit_id[1]);
            } else {
                $query->where('properties.id', $property_id_unit_id[0]);
            }
        }
        if (!empty($request['search'])) {
            $query->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('states.code', '=', \Request::get('search'))
                    ->orWhere('properties.zip', '=', \Request::get('search'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.email', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.firstname', 'like', '%' . \Request::get('search') . '%')
                    ->orWhere('leases.lastname', 'like', '%' . \Request::get('search') . '%');
            });
        }
        //advanced filter
        if (!empty($request['due_date'])) {
            $query->where('invoices.due_date', '=', $request['due_date']);
        }
        if (!empty($request['balance'])) {
            $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount = ?', [$request['balance']]);
        }
        if (!empty($request['full_user'])) {
            $query->where(function($q) {
                $q->where('leases.firstname', 'like', '%' . \Request::get('full_user') . '%')
                    ->orWhere('leases.lastname', 'like', '%' . \Request::get('full_user') . '%');
            });
        }
        if (!empty($request['email'])) {
            $query->where('leases.email', 'like', '%' . $request['email'] . '%');
        }
        if (!empty($request['property_unit'])) {
            $query->where(function($q) {
                $q->where('properties.address', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('states.code', '=', \Request::get('property_unit'))
                    ->orWhere('properties.zip', '=', \Request::get('property_unit'))
                    ->orWhere('properties.city', 'like', '%' . \Request::get('property_unit') . '%')
                    ->orWhere('units.name', 'like', '%' . \Request::get('property_unit') . '%');
            });
        }
        if (!empty($request['description'])) {
            $query->where('invoices.description', 'like', '%' . $request['description'] . '%');
        }
        //--

        $query->select(
            'leases.email',
            DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
            'invoices.due_date',
            'invoices.description',
            DB::raw('invoices.id AS invoice_id'),
            'leases.unit_id',
            DB::raw('leases.id AS lease_id'),
            DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
            DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit'),
            DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance')
        );

        $query->union($query1);

        //Sorting
        $availableColumns = [
            'full_user',
            'email',
            'property_unit',
            'due_date',
            'description',
            'balance'
        ];
        $column = $request->get('column');
        if (in_array($column, $availableColumns)) {
            if ($request->get('order') === 'asc') {
                $query->orderBy($column, 'asc');
            } else {
                $query->orderBy($column, 'desc');
            }
        } else {
            $query->orderBy('due_date', 'desc');
        }
        $invoices = $query->paginate(50);

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $properties_query = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, 0 AS unit_id, "" AS unit_name'));
            $properties_units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->select(DB::raw('properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->union($properties_query)
                ->orderBy('property_id', 'asc')
                ->orderBy('unit_id', 'asc')
                ->get();

            $units = Unit::join('properties', 'properties.id', '=', 'units.property_id')->where('properties.user_id', Auth::user()->id)
                ->orderBy('properties.id', 'asc')
                ->orderBy('units.id', 'asc')
                ->select(['units.*', 'units.id AS unit_id'])
                ->get();
        } else {
            $properties_units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->join('properties', 'properties.id', '=', 'units.property_id')
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT properties.id AS property_id, properties.address AS property_address, units.id AS unit_id, units.name AS unit_name'))
                ->get();

            $units = Unit::join('leases', 'leases.unit_id', '=', 'units.id')->where('leases.email', Auth::user()->email)
                ->orderBy('units.id', 'asc')
                ->select(DB::raw('DISTINCT units.*'))
                ->get();
        }

        return view(
            'payments.invoices',
            [
                'invoices' => $invoices,
                'units' => $units,
                'properties_units' => $properties_units,
                'advanced_filter' => $request['advanced_filter'] ?? "0",
                'report_type' => $request['paid'] ? "2" : ($request['unpaid'] ? "3" : ($request['upcoming'] ? "4" : "1")),
                'report_text' => $request['paid'] ? "Paid Invoices" : ($request['unpaid'] ? "Unpaid / Partially Paid Invoices" : ($request['upcoming'] ? "Upcoming Invoices" : "Invoices")),
                'additional_params' => $request['paid'] ? ['paid'=>'paid'] : ($request['unpaid'] ? ['unpaid'=>'unpaid'] : ($request['upcoming'] ? ['upcoming'=>'upcoming'] : [])),
            ]
        );
    }

    public function ajaxDetails(Request $request){
        $invoice = Invoice::find($request->invoice_id);
        return view('payments.ajax-details',compact('invoice'));
    }

    public function ajaxTenantGetInvoices(Request $request)
    {

        $query1 = DB::table('invoices');
        $query1->leftJoin(
            DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
            'invoices.id', '=', 'p.invoice_id'
        );
        //invoice based on lease
        $query1->join('leases', 'leases.id', '=', 'invoices.base_id');
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query1->join('units', 'units.id', '=', 'leases.unit_id');
            $query1->join('properties', 'properties.id', '=', 'units.property_id');
            $query1->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query1->where('leases.email', '=', Auth::user()->email);
        }
        $query1->where('is_lease_pay', '=', '1');
        $query1->where('leases.id', '=', $request->get('lease_id'));

        //easy filter
        if (!empty($request['paid'])) {
            if ($request['paid'] == 'paid'){
                $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount = 0');
            } else {
                $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
            }
        }
        //advanced filter
        if (!empty($request['due_date'])) {
            $query1->where('invoices.due_date', '=', $request['due_date']);
        }
        if (!empty($request['description'])) {
            $query1->where('invoices.description', 'like', '%' . $request['description'] . '%');
        }
        if (!empty($request['amount'])) {
            $query1->whereRaw('invoices.amount = ?', [$request['amount']]);
        }
        if (!empty($request['balance'])) {
            $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount = ?', [$request['balance']]);
        }

        $query1->select(
            'invoices.id',
            'invoices.due_date',
            'leases.unit_id',
            'invoices.amount',
            'invoices.pay_month',
            'invoices.description',
            'invoices.is_late_fee',
            'invoices.is_lease_pay',
            DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
            DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance')
        );

        $query = DB::table('invoices');
        $query->leftJoin(
            DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
            'invoices.id', '=', 'p.invoice_id'
        );
        // invoice based on bill
        $query->join('bills', 'bills.id', '=', 'invoices.base_id');
        $query->join('leases', 'leases.id', '=', 'bills.lease_id');
        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query->join('units', 'units.id', '=', 'leases.unit_id');
            $query->join('properties', 'properties.id', '=', 'units.property_id');
            $query->where('properties.user_id', '=', Auth::user()->id);
        } else {
            $query->where('leases.email', '=', Auth::user()->email);
        }
        $query->where('is_lease_pay', '=', '0');
        $query->where('leases.id', '=', $request->get('lease_id'));

        //easy filter
        if (!empty($request['paid'])) {
            if ($request['paid'] == 'paid'){
                $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount = 0');
            } else {
                $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
            }
        }
        //advanced filter
        if (!empty($request['due_date'])) {
            $query->where('invoices.due_date', '=', $request['due_date']);
        }
        if (!empty($request['description'])) {
            $query->where('invoices.description', 'like', '%' . $request['description'] . '%');
        }
        if (!empty($request['amount'])) {
            $query->whereRaw('invoices.amount = ?', [$request['amount']]);
        }
        if (!empty($request['balance'])) {
            $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount = ?', [$request['balance']]);
        }

        $query->select(
            'invoices.id',
            'invoices.due_date',
            'leases.unit_id',
            'invoices.amount',
            'invoices.pay_month',
            'invoices.description',
            'invoices.is_late_fee',
            'invoices.is_lease_pay',
            DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
            DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance')
        );

        $query->union($query1);

        //Sorting
        $availableColumns = [
            'due_date',
            'description',
            'amount',
            'balance'
        ];
        $column = $request->get('column');
        if (in_array($column, $availableColumns)) {
            if ($request->get('order') === 'asc') {
                $query->orderBy($column, 'asc');
            } else {
                $query->orderBy($column, 'desc');
            }
        }
        $invoices = $query->paginate(10);

        $lease = Lease::find($request->get('lease_id'));


        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            return view(
                'payments.ajax-landlord-invoices',
                [
                    'invoices' => $invoices,
                    'lease' => $lease,
                ]
            );
        } else {
            return view(
                'payments.ajax-tenant-invoices',
                [
                    'invoices' => $invoices,
                    'lease' => $lease,
                ]
            );
        }
    }


}
