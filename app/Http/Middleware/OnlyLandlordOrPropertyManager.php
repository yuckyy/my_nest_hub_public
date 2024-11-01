<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Role;

class OnlyLandlordOrPropertyManager
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
        if (
            Auth::check() &&
            ( Auth::user()->roles()->get()[0]->id == 2 || Auth::user()->roles()->get()[0]->id == 3 )
        ) {
            return $next($request);
        } else {
            abort(404);
        }
    }
}
