<?php

namespace App\Http\Controllers\Tenant\Leases;

use App\Http\Requests\AddApplicationRequest;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use App\Repositories\Contracts\LeasesRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    private $lr;

    public function __construct(LeasesRepositoryInterface $lr) {
        $this->lr = $lr;
    }

    public function index(Request $request) {
        if (!Auth::user()->isTenant()) {
            return redirect()->intended('dashboard');
        }

        //$leases = $this->lr->getAll($request->all());
        $activeLeases = Lease::where('email', Auth::user()->email)->get();
        $inactiveLeases = Lease::onlyTrashed()->where('email', Auth::user()->email)->get();

        if($request['lease_id']){
            $selectedLease = Lease::withTrashed()
                ->where('id', $request['lease_id'])
                ->first();
        } else {
            $selectedLease = $activeLeases->first();
            if(!$selectedLease){
                $selectedLease = Auth::user()->leases->first();
            }
        }

        return view(
            'tenant.leases.index',
            [
                'selectedLease' => $selectedLease ?? false,
                'activeLeases' => $activeLeases ?? false,
                'inactiveLeases' => $inactiveLeases ?? false,
            ]
        );
    }

}
