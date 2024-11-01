<?php

namespace App\Http\Middleware;

use App\Models\Invoice;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Role;

class EnsureUserIsOwnerOfInvoice
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
        $invoice_id = $request['id'] ??
            $request['invoice_id'] ??
            $request['invoice'] ??
            $request->route('id') ??
            $request->route('invoice_id') ??
            $request->route('invoice');
        $invoice = Invoice::find($invoice_id);
        if(!empty($invoice)){
            $lease = $invoice->lease;
            if($lease->unit->property->user->id == Auth::user()->id){
                return $next($request);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
}
