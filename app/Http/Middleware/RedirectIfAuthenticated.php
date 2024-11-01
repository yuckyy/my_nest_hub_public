<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();

            $roles = $user->roles()->get();
            for ($i = 0; $i < count($roles); $i++) {
                if (
                    $roles[$i]->name === 'Landlord' ||
                    $roles[$i]->name === 'Property manager'
                ) {
                    return redirect()->intended('properties');
                }
            }

            return redirect()->intended('profile');
        }

        return $next($request);
    }
}
