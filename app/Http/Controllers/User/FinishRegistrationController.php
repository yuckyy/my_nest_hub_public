<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\FinishRegistrationRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\SubscriptionPlan;
use App\Models\Coupon;

class FinishRegistrationController extends Controller
{
    //
    private $ur;

    public function __construct(UserRepositoryInterface $ur) {
        $this->ur = $ur;
    }

    public function view(string $email) {

        $token = Str::random(60);
        $passwordResetQ = DB::table('password_resets')->where('email', $email)->delete();
        DB::table('password_resets')->insert([
            'email' => trim($email),
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        return view('auth.finish_registration', compact('email', 'token'));
    }

    public function save(FinishRegistrationRequest $request) {

        $passwordResetQ = DB::table('password_resets')->where('email', $request->email);
        $passwordReset = $passwordResetQ->first();
        if ($passwordReset->token != $request->token)
            return redirect()->back()->withErrors('Authorisation Error');

        $user = $this->ur->getByColumn('email', $request->email)->first();
        $user->update([
            'last_login_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);
        $user = $this->ur->updateColumn('password', Hash::make($request->password), $user->id);
        Auth::login($user);
        DB::table('password_resets')->where('email', $request->email)->delete();
        return redirect()->route('dashboard');
    }

    public function membership()
    {
        return view(
            'auth.membership',
            [
                'plansToShow' => SubscriptionPlan::where('show_plan',1)->get(),
            ]
        );
    }

    public function subscribe($plan_id)
    {
        $plan = SubscriptionPlan::where([['show_plan',1],['id',$plan_id]])->whereNotNull('stripe_plan_id')->first();
        if (!$plan) {
            abort('404');
        }
        return view(
            'auth.subscribe',
            [
                'plan' => $plan
            ]
        );
    }

    public function applyCode(Request $request)
    {
        $coupon = Coupon::where('code',$request->code)->first();

        if (!isset($request->code) || $request->code == "" || !$coupon) {
            $output = ['error' => 'Invalid Coupon Code.', 'old_total' => $request->total];
            return response()->json($output, 200);
        }
        $output = ['success' => 'Coupon Applied!', 'new_total' => $request->total - $coupon->discount];
        return response()->json($output, 200);
    }
}
