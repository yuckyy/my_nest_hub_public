<?php

namespace App\Http\Middleware;

use App\Models\Unit;
use App\Models\Lease;
use App\Models\Bill;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Role;

class EnsureUserIsOwnerOfBill
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $bill_id = $request['id'] ??
            $request['bill_id'] ??
            $request['bill'] ??
            $request->route('id') ??
            $request->route('bill_id') ??
            $request->route('bill');
        $bill = Bill::find($bill_id);
        if (!empty($bill) && $bill->lease->unit->property->user->id == Auth::user()->id) {
            return $next($request);
        } else {
            abort(404);
        }
    }
}
