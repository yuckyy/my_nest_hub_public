<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Controllers\Maintenance\IndexController;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Lease;
use App\Models\User;
use App\Models\Financial;
use App\Models\RequestFeature;
use App\Models\MaintenanceRequestStatus;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Notifications\RequestFeatureToAdmin;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth'/*, 'verified'*/]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = MaintenanceRequest::query();

        if (Auth::user()->isLandlord() || Auth::user()->isPropManager()) {
            $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
            $query->join('properties', 'properties.id', '=', 'units.property_id');
            $query->where('properties.user_id', Auth::id());
        } else {
            $query->join('units', 'units.id', '=', 'maintenance_requests.unit_id');
            $query->join('leases', 'leases.unit_id', '=', 'units.id');
            $query->where('leases.email', Auth::user()->email);
        }
        $query->where('maintenance_requests.archived', 0);

        $queryNew = clone $query;
        $queryNew->where('maintenance_requests.status_id', '1');
        $countNew = $queryNew->count();

        $queryInProgress = clone $query;
        $queryInProgress->where('maintenance_requests.status_id', '2');
        $countInProgress = $queryInProgress->count();

        $queryResolved = clone $query;
        $queryResolved->where('maintenance_requests.status_id', '3');
        $countResolved = $queryResolved->count();

        if ($user->isTenant()) {

            $lease = Lease::where('email', $user->email)->orderBy('created_at', 'desc')->first();
            if (empty($lease)) {
                $lease = Lease::withTrashed()->where('email', $user->email)->orderBy('created_at', 'desc')->first();
            }
            $leaseCount = Lease::where('email', $user->email)->orderBy('created_at', 'desc')->count();

            return view(
                'dashboard.index',
                [
                    'countNew' => $countNew,
                    'countInProgress' => $countInProgress,
                    'countResolved' => $countResolved,

                    'user' => $user,
                    'lease' => $lease,
                    'leaseCount' => $leaseCount
                ]
            );
        } else if ($user->isLandlord() || $user->isPropManager()) {
            /*
            // keep it for a while. may need for performance optimizing

            $query1 = DB::table('invoices');
            $query1->leftJoin(
                DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
                'invoices.id', '=', 'p.invoice_id'
            );
            $query1->join('leases', 'leases.id', '=', 'invoices.base_id');
            $query1->join('units', 'units.id', '=', 'leases.unit_id');
            $query1->join('properties', 'properties.id', '=', 'units.property_id');
            $query1->leftJoin('states', 'states.id', '=', 'properties.state_id');
            $query1->where('is_lease_pay', '=', '1');
            $query1->where('properties.user_id', '=', Auth::user()->id);
            $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
            $query1->select(
                'leases.email',
                DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
                'invoices.due_date',
                'leases.unit_id',
                DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
                DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit'),
                DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance')
            );

            $query = DB::table('invoices');
            $query->leftJoin(
                DB::raw('( SELECT invoice_id, SUM(amount) AS paid FROM payments GROUP BY invoice_id ) p'),
                'invoices.id', '=', 'p.invoice_id'
            );
            $query->join('bills', 'bills.id', '=', 'invoices.base_id');
            $query->join('leases', 'leases.id', '=', 'bills.lease_id');
            $query->join('units', 'units.id', '=', 'leases.unit_id');
            $query->join('properties', 'properties.id', '=', 'units.property_id');
            $query->leftJoin('states', 'states.id', '=', 'properties.state_id');
            $query->where('is_lease_pay', '=', '0');
            $query->where('properties.user_id', '=', Auth::user()->id);
            $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
            $query->select(
                'leases.email',
                DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
                'invoices.due_date',
                'leases.unit_id',
                DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
                DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit'),
                DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance')
            );

            $query->union($query1);

            // keep it. may need for request optimisation
            //$countQuery = clone $query;
            //$totalBalance = $countQuery->get()->sum('balance');
            //$totalDeposit = $countQuery->get()->sum('paid_amount');

            //Sorting
            $availableColumns = [
                'full_user',
                'email',
                'property_unit',
                'due_date',
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

            $invoices = $query->paginate(5);
            */

            $plan = Auth::user()->activePlan() ? SubscriptionPlan::find(Auth::user()->activePlan()->plan_id) : SubscriptionPlan::freeTrial();
            return view(
                'dashboard.index',
                [
                    //'invoices' => $invoices,
                    'user' => $user,

                    'countNew' => $countNew,
                    'countInProgress' => $countInProgress,
                    'countResolved' => $countResolved,
                    'plan' => $plan
                ]
            );
        }

        return redirect('profile');
    }

    public function ajaxLandlordGetNegativeInvoices(Request $request)
    {
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
        $query1->leftJoin('users', 'users.email', '=', 'leases.email');
        $query1->where('is_lease_pay', '=', '1');
        $query1->where('properties.user_id', '=', Auth::user()->id);
        $query1->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
        $query1->select(
            'leases.email',
            DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
            'invoices.id',
            'invoices.due_date',
            'leases.unit_id',
            DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
            DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit'),
            DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance'),
            DB::raw('users.last_login_at AS tenant_last_login'),
            DB::raw('leases.id AS lease_id')
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
        $query->leftJoin('users', 'users.email', '=', 'leases.email');
        $query->where('is_lease_pay', '=', '0');
        $query->where('properties.user_id', '=', Auth::user()->id);
        $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
        $query->select(
            'leases.email',
            DB::raw('CONCAT(leases.firstname, " ", leases.lastname) AS full_user'),
            'invoices.id',
            'invoices.due_date',
            'leases.unit_id',
            DB::raw('IFNULL(p.paid, 0) AS paid_amount'),
            DB::raw('CONCAT(properties.address, ", ", properties.city, ", ", states.code, ", ", properties.zip, ", ", units.name) AS property_unit'),
            DB::raw('(IFNULL(p.paid, 0) - invoices.amount) AS balance'),
            DB::raw('users.last_login_at AS tenant_last_login'),
            DB::raw('leases.id AS lease_id')
        );

        $query->union($query1);

        // keep it. may need for request optimisation
        //$countQuery = clone $query;
        //$totalBalance = $countQuery->get()->sum('balance');
        //$totalDeposit = $countQuery->get()->sum('paid_amount');

        //Sorting
        $availableColumns = [
            'full_user',
            'email',
            'property_unit',
            'due_date',
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
        $invoices = $query->paginate(50);

        return view(
            'dashboard.ajax-negative-invoices',
            [
                'invoices' => $invoices,
            ]
        );
    }

    public function requestFeature()
    {
        return view(
            'dashboard.request-feature'
        );
    }

    public function requestFeatureSave(Request $request)
    {
        $user = Auth::user();
        $feature = new RequestFeature();
        $feature->request = $request->get('request');
        $feature->user_id = $user->id;
        $feature->save();

        $adminEmail = env('REQUEST_FEATURE_EMAIL', 'yaroslavkondratenko54@gmail.com');
        $admins = User::where('email', $adminEmail)
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new RequestFeatureToAdmin($request->get('request'), $user));
        }

        return redirect()->route('dashboard')->with('success', 'Thank you for sharing your thoughts!');
    }

    public function reports()
    {
        return view(
            'dashboard.reports'
        );
    }

}
