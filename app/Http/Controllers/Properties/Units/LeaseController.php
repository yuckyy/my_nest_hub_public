<?php

namespace App\Http\Controllers\Properties\Units;

use App\Http\Requests\AddLeaseRequest;
use App\Http\Requests\EditLeaseRequest;
use App\Jobs\EndLease;
use App\Models\Application;
use App\Models\Role;

use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use App\Repositories\Contracts\PropertiesRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\Bill;
use App\Models\MoveIn;
use App\Models\FinanceUnit;
use Carbon\Carbon;
use App\Rules\LeaseStartDate;
use App\Rules\LeaseEndDate;
use App\Rules\MonthToMonth;
use App\Rules\RentAssistance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Auth;

class LeaseController extends Controller
{
    //
    private $ur;
    private $lr;

    private $ar;

    public function __construct(ApplicationsRepositoryInterface $ar,UserRepositoryInterface $ur, LeasesRepositoryInterface $lr) {
        $this->ur = $ur;
        $this->lr = $lr;
        $this->ar = $ar;
    }

    public function index($unit, Request $request) {

        $request->session()->forget('lease_add_step');

        $unitObject = Unit::find($unit);
        if (!$unitObject) {
            abort(404);
        }
        $unitID = ['unit_id'=>$unit];
        $applicationsData = $this->ar->getWithoutPaginate($request->all() + $unitID);
        $applicationsCount = $applicationsData['applications']->count();
//        var_dump($applicationsCount);
//        die;
        $activeLeases = Lease::where('unit_id', $unit)->get();
        $inactiveLeases = Lease::onlyTrashed()->where('unit_id', $unit)->get();

        if($request['lease_id']){
            $selectedLease = Lease::withTrashed()
                ->where('id', $request['lease_id'])
                ->first();
        } else {
            $selectedLease = $activeLeases->first();
            if(!$selectedLease){
                $selectedLease = $unitObject->leases->first();
            }
        }

        if($selectedLease) {
            //---
            //check if we may change start date
            $query1 = DB::table('payments');
            $query1->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id');
            //invoice based on lease
            $query1->join('leases', 'leases.id', '=', 'invoices.base_id');
            $query1->where('is_lease_pay', '=', '1');
            $query1->where('leases.id', '=', $selectedLease->id);
            $query1->where('payment_method', '!=', 'Generated');
            $leasePaymentsCount = $query1->count();

            $query = DB::table('invoices');
            // invoice based on bill
            $query->join('bills', 'bills.id', '=', 'invoices.base_id');
            $query->join('leases', 'leases.id', '=', 'bills.lease_id');
            $query->where('is_lease_pay', '=', '0');
            $query->where('leases.id', '=', $selectedLease->id);
            $billsCount = $query->count();

            $startDateEditable = ($leasePaymentsCount == 0) && ($billsCount == 0);
            //---
        }

        return view(
            'properties.units.leases.index',
            [
                'applicationsCount' => $applicationsCount,
                'unit' => $unitObject,
                'updatedElement' => $request['updated_element'] ?? "undefined",

                'selectedLease' => $selectedLease ?? false,
                'activeLeases' => $activeLeases ?? false,
                'inactiveLeases' => $inactiveLeases ?? false,
                'startDateEditable' => $startDateEditable ?? false,
        ]
        );
    }

}
