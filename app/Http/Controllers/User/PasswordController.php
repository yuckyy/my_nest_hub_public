<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    //
    public function edit(Request $request) {
        return view(
            'user.password',
            [
                'success' => false,
            ]
        );
    }

    public function save(Request $request) {
        $user = Auth::user();

        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        return $fail('The specified password does not match the database password');
                    }
                }
            ],
            'new_password' => 'required|min:8|string|different:current_password|regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
            'confirm_password' => 'required|min:8|string|same:new_password',
        ]);

        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        return view(
            'user.password',
            [
                'success' => true,
            ]
        );
    }
}
