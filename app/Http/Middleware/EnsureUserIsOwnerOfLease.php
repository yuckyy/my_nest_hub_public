<?php

namespace App\Http\Middleware;

use App\Models\Unit;
use App\Models\Lease;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Role;

class EnsureUserIsOwnerOfLease
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
        $lease_id = $request['id'] ??
            $request['lease_id'] ??
            $request['lease'] ??
            $request->route('id') ??
            $request->route('lease_id') ??
            $request->route('lease');
        $lease = Lease::find($lease_id);
        if(!empty($lease)){
            if ($lease->unit->property->user->id == Auth::user()->id) {
                return $next($request);
            } else {
                abort(404);
            }
        } else {
            // here we check a unit by id, not a lease
            $unit_id = $request->route('unit');
            $unit = Unit::find($unit_id);
            if (!empty($unit) && $unit->property->user->id == Auth::user()->id) {
                return $next($request);
            } else {
                abort(404);
            }
        }
    }
}
