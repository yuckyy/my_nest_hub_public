<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/profile';


    public $maxAttempts = 5; // change to the max attemp you want.
    public $decayMinutes = 1; // change to the minutes you want.


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);

        if ($user->isAdmin()) {
            $request->session()->forget('adminLoginAsUser');
        }

        $roles = $user->roles()->get();
        for ($i = 0; $i < count($roles); $i++) {
            if (
                $roles[$i]->name === 'Landlord' ||
                $roles[$i]->name === 'Property manager'
            ) {
                //return redirect()->intended('dashboard');
                return redirect()->route('dashboard');
            }
        }

        if ($user->isTenant()) {
            $intended_url = redirect()->intended()->getTargetUrl();
            if (strpos($intended_url, 'applications/add') === false) {

                if (($user->applications->count() == 0) && ($user->leases->count() == 0)) {
                    return redirect()->route('dashboard')
                        ->with('success', 'Welcome to MYNESTHUB!')
                        ->with('whatsnext', 'Your account is ready to go! You can complete your application and send it to a potential landlord. On the left-hand side of the menu, click on Applications. Watch this video to see how to complete your application.')
                        ->with('gif', url('/') . '/images/help/tenant-registration-complete-email-confirmed-whats-next.gif');
                }
                if (($user->applications->count() > 0) && ($user->leases->count() == 0)) {
                    return redirect()->route('dashboard')
                        ->with('success', 'Welcome to MYNESTHUB!')
                        ->with('whatsnext', 'It seems like you created your application and it’s ready to be shared with your potential landlord. On the left-hand side of the menu, click on “Applications” and then click on the “Share Application” icon. Watch this video to see how to share an application.')
                        ->with('gif', url('/') . '/images/help/tenant-how-to-share-application-with-landlord.gif');
                }
                return redirect()->route('dashboard');
            }
            //return redirect()->route('dashboard',['x'=>$intended_url]);
            return redirect()->to($intended_url);
        }

        //return redirect()->intended('dashboard');
        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            auth()->logoutOtherDevices($request->password);
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}
