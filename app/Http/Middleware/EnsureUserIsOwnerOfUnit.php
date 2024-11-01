<?php

namespace App\Http\Middleware;

use App\Models\Unit;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Role;

class EnsureUserIsOwnerOfUnit
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
        $unit_id = $request['id'] ??
            $request['unit_id'] ??
            $request['unit'] ??
            $request->route('id') ??
            $request->route('unit_id') ??
            $request->route('unit');
        $unit = Unit::find($unit_id);
        if(!empty($unit) && $unit->property->user->id == Auth::user()->id){
            return $next($request);
        } else {
            abort(404);
        }
    }
}
