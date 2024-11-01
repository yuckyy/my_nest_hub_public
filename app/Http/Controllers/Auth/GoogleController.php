<?php

namespace App\Http\Controllers\Auth;

//use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//use App\Models\User;
use Carbon\Carbon;

//
//use App\Models\Role;
////use App\Models\User;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Hash;
//use Laravel\Socialite\Facades\Socialite;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class GoogleController extends Controller
{
    use AuthenticatesUsers;

    use AuthenticatesUsers;

    protected function redirectTo()
    {
        return '/dashboard';
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $user = Socialite::driver('google')->user();

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            Auth::login($existingUser, true);
        } else {
            $name = $user->getName();

            $originalName = $name;

            $parts = explode(' ', $name, 2);

            if (count($parts) > 1) {
                [$name, $last_name] = $parts;
            } else {
                $name = $originalName;
                $last_name = '';
            }
//            var_dump($name);
//            var_dump($last_name);
//            die;
            $date = Carbon::now();
            $newUser = User::create([
                'name' => $name,
                'last_name' => $last_name,
                'email' => $user->getEmail(),
                'email_verified_at' => $date,
                'password' => bcrypt('randompassword')
            ]);
            $data['role'] = [
                '2',
                'Landlord'
            ];
            $role = Role::find($data['role']);
            $newUser->roles()->attach($role);
            $newUser->save();


            Auth::login($newUser, true);
        }

        return redirect($this->redirectPath());
    }


    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(Request $request)
    {
//         $user = Socialite::driver('facebook')->user();
        // Добавьте код для создания пользователя или входа в существующего пользователя.
        $user = Socialite::driver('facebook')->user();

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            Auth::login($existingUser, true);
        } else {
            $name = $user->getName();
            $last_name = $name;
            $name = stristr($name, ' ', true);
            $last_name = stristr($last_name, ' ');
//             var_dump($last_name);
//             var_dump($name);
//             die;
            $date = Carbon::now();
            $newUser = User::create([
                'name' => $name,
                'last_name' => $last_name,
                'email' => $user->getEmail(),
                'email_verified_at' => $date,
                'password' => bcrypt('randompassword')
            ]);
            $data['role'] = [
                '2',
                'Landlord'
            ];
            $role = Role::find($data['role']);
            $newUser->roles()->attach($role);
            $newUser->save();


            Auth::login($newUser, true);
        }

        return redirect($this->redirectPath());
    }

}

;
