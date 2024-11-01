<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\DatabaseManager;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'name' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => [
                    'required',
                    'string',
                    'min:12',
                    'confirmed',
                    'regex:/[a-zA-Z]/',      // must contain at least one letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'password_confirmation' => ['required', 'string', 'min:12', 'same:password'],
                'accept_tos' => ['accepted'],
                'g-recaptcha-response' => 'required|captcha'
            ],
            [
                'password.regex' => 'Password must include at least one letter, one number and one special character.',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $name = preg_replace('/[^a-zA-Z0-9\s\-()`\',.]/', '', $data['name']);
        $name = str_replace('.','. ', $name);
        $name = implode(' ', array_slice(explode(' ', $name), 0, 4));

        $lastname = preg_replace('/[^a-zA-Z0-9\s\-()`\',.]/', '', $data['lastname']);
        $lastname = str_replace('.','. ', $lastname);
        $lastname = implode(' ', array_slice(explode(' ', $lastname), 0, 4));

        $user = User::create([
            'name' => ucfirst(strtolower($name)),
            'lastname' => ucfirst(strtolower($lastname)),
            'email' => $data['email'],
            'intended_url' => $data['intended_url'] ?? "",
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::find($data['role']);
        $user->roles()->attach($role);
        $user->save();

        return $user;
    }

    protected function registered(Request $request, $user)
    {
        return redirect('profile');
    }
}
