<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            return $request->expectsJson()
                    ? abort(403, 'Your email address is not verified.')
                    : Redirect::route($redirectToRoute ?: 'verification.notice');
        }

        if (Auth::user()->roles()->get()[0]->id != 2 && Auth::user()->roles()->get()[0]->id != 3 || Auth::user()->activePlan()) {
            return $next($request);
        }

        // only landlord goes here
        if ((new \Carbon\Carbon(Auth::user()->free_trial_started_at))->diffInDays() <= 14) {
            return $next($request);
        }
        //force landlord to enter credit card
        return redirect()->route('registration/membership');
    }
}
