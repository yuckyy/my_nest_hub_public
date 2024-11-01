<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    public function ajaxSet(Request $request)
    {
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'message' => 'unauthorised',
            ], 400);
        }

        $settings = UserSetting::where('user_id',$user->id)->where('key',$request->key)->first();
        if(empty($settings)){
            $settings = new UserSetting;
            $settings->user_id = $user->id;
            $settings->key = $request->key;
        }
        $settings->value = $request->value;
        $settings->save();

        return response()->json([
            'id' => $settings->id,
        ], 200);
    }
}
