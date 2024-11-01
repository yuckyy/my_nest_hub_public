<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\StripeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Grids\UsersGridInterface;

class AdminController extends Controller
{
    public function users(UsersGridInterface $usersGrid, Request $request)
    {
        $query = User::query();

        if (!$request->has('sort_by') || $request->get('sort_by') == '') {
            $query->orderBy('created_at','desc');
        } elseif ($request->get('sort_by') == 'role_id') {
            $dir = $request->get('sort_dir');
            $query->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
                    ->join('roles', 'roles.id', '=', 'users_roles.role_id')
                    ->orderBy('roles.name',$dir);
        }

        if ($request->has('role_id') && $request->get('role_id') != '') {
            $query->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')->where('role_id',$request->get('role_id'));
        }

        return $usersGrid
                    ->create(['query' => $query, 'request' => $request])
                    ->renderOn('admin.users');
    }

    public function loginAsUser(Request $request)
    {
        Auth::loginUsingId($request->user_id);
        $request->session()->put('adminLoginAsUser', true);

        return redirect()->route('profile');
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->user_id);

        return view('admin.delete-user',['user' => $user]);
    }
    public function deleteUserSubmit(Request $request)
    {
        if(Auth::user()->id == $request->user_id){
            return redirect()->route('users')->with('message','You can\'t delete yourself');
        }
        $user = User::find($request->user_id);

        $subscriptionService = new StripeSubscriptionService;
        if ($user->activePlan()) {
            try {
                $subscriptionService->cancelSubscription($user->activePlan()->stripe_subscription_id);
            } catch (\Exception $e) {
                // return back()->withInput()->with('error',$e->getMessage());
            }
            // $user->activePlan()->update([
            //     'stripe_subscription_status' => 'canceled'
            // ]);
            // $user->activePlan()->delete();
        }

        if($user->properties->count() > 0) {
            foreach ($user->properties as $property) {
                Property::deleteProperty($property->id);
            }
        }
        $user->delete();
        return redirect()->route('users')->with('message','User successfully deleted');
    }
}
