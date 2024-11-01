<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;

class  ProfileController extends Controller
{
    //
    public function edit(Request $request) {
        if (!empty(session()->get('redirect_link'))) {
            $url = session()->get('redirect_link');
            session()->forget('redirect_link');
            return redirect($url);
        }

        $intended_url = urldecode(Auth::user()->intended_url);
        $user = Auth::user();
        if(!empty($user->intended_url) && ($user->intended_url != url('/'))){
            $user->intended_url = "";
            $user->save();

            if(strpos($intended_url,"applications/add") > 0){
                return redirect($intended_url)->with('success','Please Create Your Application.');
            } else {
                return redirect($intended_url);
            }
        }

        return view(
            'user.index',
            [
                'user' => Auth::user(),
                'success' => false,
            ]
        );
    }

    public function save(Request $request) {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|max:50|string',
            'lastname' => 'required|max:50|string',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = ucfirst(strtolower($request->get('name')));
        $user->lastname = ucfirst(strtolower($request->get('lastname')));
        $user->phone = $request->get('phone') ?? "";
        //$user->email = $request->get('email');
        $user->save();

        return view(
            'user.index',
            [
                'user' => $user,
                'success' => true,
            ]
        );
    }

    public function photo() {
        return view(
            'user.photo',
            [
                'user' => Auth::user(),
                'success' => false,
            ]
        );
    }

    public function emailPreferences(Request $request) {
        $user = Auth::user();
        if($user->isTenant()){
            $emailPreferenceList = Preference::tenantEmailPreferencesList();
        } else {
            $emailPreferenceList = Preference::landlordEmailPreferencesList();
        }
        return view(
            'user.email-preferences',
            [
                'user' => $user,
                'emailPreferenceList' =>$emailPreferenceList,
            ]
        );
    }

    public function emailPreferencesSave(Request $request) {
        $user = Auth::user();
        if($user->isTenant()){
            $emailPreferenceList = Preference::tenantEmailPreferencesList();
        } else {
            $emailPreferenceList = Preference::landlordEmailPreferencesList();
        }
        $preferences = $user->preferences;
        if(empty($preferences)){
            //create preferences
            $preferences = new Preference;
            $preferences->user_id = $user->id;
            $preferences->unsubscribe_token = $user->id.md5($user->id.rand(0, 99999999));
            $preferences->save();
        }
        foreach($emailPreferenceList as $pref){
            $preferences->{$pref['field']} = ($request->{$pref['field']} == '1');
        }
        $preferences->save();
        return redirect()->route('profile/email-preferences')->with('success', 'Your Email Preferences have been updated');
    }

    public function unsubscribe(Request $request) {
        $preferences = Preference::where('unsubscribe_token',$request->unsubscribe_token)->first();
        if (!$preferences) {
            abort('404');
        }

        $emailPreferenceList = Preference::tenantEmailPreferencesList();
        foreach($emailPreferenceList as $pref){
            $preferences->{$pref['field']} = 0;
        }
        $emailPreferenceList = Preference::landlordEmailPreferencesList();
        foreach($emailPreferenceList as $pref){
            $preferences->{$pref['field']} = 0;
        }
        $preferences->save();

        return redirect()->route('unsubscribe-complete');
    }

    public function unsubscribeComplete(Request $request) {
        return view('user.unsubscribe-complete');
    }

}
