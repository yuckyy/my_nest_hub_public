<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Preference;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        //TODO not in use
        //TODO actually in use: easyVerify
        //TODO schedule to remove
        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            $user = Auth::user();
            if(!$user->isTenant()){
                //only landlord. registration will be finished with the membership controller
                //return redirect($this->redirectPath());

                //changed to free trial
                return redirect()->route('properties')
                    ->with('success','Your membership had been successfully updated.')
                    ->with('whatsnext','You didn\'t create any properties yet. Press "Add New Property" to create a new property.')
                    ->with('gif', url('/').'/images/help/email-verified-create-property.gif');
            }
        } else {
            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        }

        $user = Auth::user();
        if($user->isTenant()){

            if($user->leases->count() > 0){
                return redirect()->route('dashboard')
                    ->with('success','Your registration has been successfully completed.');
            }

            $intended_url = urldecode($user->intended_url);
            if(!empty($intended_url) && ($intended_url != url('/'))){
                $user->intended_url = "";
                $user->save();

                if(strpos($intended_url,"applications/add") > 0){
                    return redirect($intended_url)->with('success','Please Create Your Application.');
                }
            }

            return redirect()->route('applications/add')
                ->with('success','Your registration has been successfully completed.')
                ->with('whatsnext','Your account is ready to go! You can complete your application and send it to a potential landlord. On the left-hand side of the menu, click on Applications. Watch this video to see how to complete your application.')
                ->with('gif', url('/').'/images/help/tenant-registration-complete-email-confirmed-whats-next.gif');
        }

        //only landlord. registration will be finished with the membership controller
        //return redirect($this->redirectPath())->with('verified', true);

        //changed to free trial
        return redirect()->route('properties')
            ->with('success','Your membership had been successfully updated.')
            ->with('whatsnext','You didn\'t create any properties yet. Press "Add New Property" to create a new property.')
            ->with('gif', url('/').'/images/help/email-verified-create-property.gif');

    }

    public function easyVerify(Request $request)
    {
        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            $user = Auth::user();
            if(!$user->isTenant()){
                //only landlord. registration will be finished with the membership controller
                //return redirect($this->redirectPath());

                //changed to free trial
                return redirect()->route('properties')
                    ->with('success','Your membership had been successfully updated.')
                    ->with('whatsnext','You didn\'t create any properties yet. Press "Add New Property" to create a new property.')
                    ->with('gif', url('/').'/images/help/email-verified-create-property.gif');
            }
        } else {
            $user = Auth::user();
            $preferences = $user->preferences;
            if(empty($preferences)){
                //create preferences
                $preferences = new Preference;
                $preferences->user_id = $user->id;
                $preferences->unsubscribe_token = $user->id.md5($user->id.rand(0, 99999999));
                $preferences->save();
            }

            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        }

        $user = Auth::user();
        if($user->isTenant()){

            if($user->leases->count() > 0){
                return redirect()->route('dashboard')
                    ->with('success','Your registration has been successfully completed.');
            }

            $intended_url = urldecode($user->intended_url);
            if(!empty($intended_url) && ($intended_url != url('/'))){
                $user->intended_url = "";
                $user->save();

                if(strpos($intended_url,"applications/add") > 0){
                    return redirect($intended_url)->with('success','Please Create Your Application.');
                }
            }

            return redirect()->route('applications/add')
                ->with('success','Your registration has been successfully completed.')
                ->with('whatsnext','Your account is ready to go! You can complete your application and send it to a potential landlord. On the left-hand side of the menu, click on Applications. Watch this video to see how to complete your application.')
                ->with('gif', url('/').'/images/help/tenant-registration-complete-email-confirmed-whats-next.gif');
        }

        //only landlord. registration will be finished with the membership controller
        //return redirect($this->redirectPath())->with('verified', true);

        //changed to free trial
        return redirect()->route('properties')
            ->with('success','Your membership had been successfully updated.')
            ->with('whatsnext','You didn\'t create any properties yet. Press "Add New Property" to create a new property.')
            ->with('gif', url('/').'/images/help/email-verified-create-property.gif');

    }
}
