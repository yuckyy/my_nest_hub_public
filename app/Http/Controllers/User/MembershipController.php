<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\UserAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Financial;
use App\Models\SubscriptionPlan;
use App\Models\UserPlan;
use App\Notifications\RequestFeatureToAdmin;
use App\Notifications\SubscriptionReceipt;
use App\Notifications\AddonReceipt;
use App\Notifications\CancelSubscription;
use App\Notifications\CancelAddon;
use App\Services\StripeSubscriptionService;
use App\Models\Addon;

class  MembershipController extends Controller
{
    private $subscriptionService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StripeSubscriptionService $subscriptionService)
    {
        $this->middleware(['auth']);

        $this->subscriptionService = $subscriptionService;
    }

    public function index(){
        return view(
            'user.membership',
            [
                'plansToShow' => SubscriptionPlan::where('show_plan',1)->get(),
                'addons' => Addon::where('active',1)->get(),
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
            'user.subscribe',
            [
                'plan' => $plan
            ]
        );
    }

    public function createSubscription(Request $request) {
        $request->validate([
            'newFinanceAccount' => 'required',
        ],[
            'newFinanceAccount.required' => 'The finance account field is required.'
        ]);
        $user = Auth::user();
        $plan = SubscriptionPlan::find($request->plan_id);
        $financeAccount = Financial::find($request->newFinanceAccount);
        $type = 'buying';

        $subscribeData = [
            'customer' => $user->customer_id,
            'default_payment_method' => $financeAccount->source_id,
            'items' => [
                ['price' => $plan->stripe_plan_id],
            ],
        ];
        if ($request->coupon_applied) {
            $subscribeData['coupon'] = $request->coupon_applied;
        }

        if (empty($user->activePlan())) {
            $subscribeData['trial_period_days'] = 14;
        }

        try {
            $sub = $this->subscriptionService->createSubscription($subscribeData);

            if ($user->activePlan()) {
                $type = 'updating';
                try {
                    $this->subscriptionService->cancelSubscription($user->activePlan()->stripe_subscription_id);
                } catch (\Exception $e) {}

                $user->activePlan()->update([
                    'stripe_subscription_status' => 'canceled'
                ]);
                $user->activePlan()->delete();
            }

            UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $request->plan_id,
                'start_date' => Carbon::createFromTimestamp($sub->current_period_start),
                'end_date' => Carbon::createFromTimestamp($sub->current_period_end),
                'stripe_subscription_id' => $sub->id,
                'stripe_subscription_status' => $sub->status,
                'coupon_code' => $request->coupon_applied ? $request->coupon_applied : null
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error',$e->getMessage());
        }
        $user->notify(new SubscriptionReceipt($plan,$type));

        if ($type == 'updating') {
            return redirect()->route('profile/membership')->with('success','Your membership had been successfully updated.');
        }

        $propertyExists = Property::where(['user_id' => $user->id])->count();
        if($propertyExists){
            return redirect()->route('properties')->with('success','Your membership had been successfully updated.');
        } else {
            return redirect()->route('properties')
                ->with('success','Your membership had been successfully updated.')
                ->with('whatsnext','You didn\'t create any properties yet. Press "Add New Property" to create a new property.')
                ->with('gif', url('/').'/images/help/email-verified-create-property.gif');
        }
    }

    public function cancelSubscription(Request $request) {
        $user = Auth::user();
        if ($user->activePlan()) {
            try {
                $this->subscriptionService->cancelSubscription($user->activePlan()->stripe_subscription_id);
            } catch (\Exception $e) {
                // return back()->withInput()->with('error',$e->getMessage());
            }
            // $user->activePlan()->update([
            //     'stripe_subscription_status' => 'canceled'
            // ]);
            // $user->activePlan()->delete();
        }
        // Notify to all tenants and landlord
        $user->notify(new CancelSubscription());
        if ($user->units_count > 0) {
            foreach ($user->getUnits() as $u) {
                if ($activeLease = $u->leases()->whereNull('deleted_at')->first()) {
                    $tenant = User::where('email', $activeLease->email)->first();
                    $tenant->notify(new CancelSubscription());
                }
            }
        }

        $user->delete();

        return back();
    }

    public function addon($addon_id)
    {
        $addon = Addon::where([['active',1],['id',$addon_id]])->whereNotNull('stripe_plan_id')->first();
        if (!$addon) {
            abort('404');
        }
        return view(
            'user.addon',
            [
                'addon' => $addon
            ]
        );
    }

    public function addonSubscribe(Request $request) {
        $request->validate([
            'newFinanceAccount' => 'required',
        ],[
            'newFinanceAccount.required' => 'The finance account field is required.'
        ]);
        $user = Auth::user();
        $addon = Addon::find($request->addon_id);
        $financeAccount = Financial::find($request->newFinanceAccount);

        $subscribeData = [
            'customer' => $user->customer_id,
            'default_payment_method' => $financeAccount->source_id,
            'items' => [
                ['price' => $addon->stripe_plan_id],
            ],
        ];
        if ($request->coupon_applied) {
            $subscribeData['coupon'] = $request->coupon_applied;
        }

        try {
            $sub = $this->subscriptionService->createSubscription($subscribeData);

            UserAddon::create([
                'user_id' => $user->id,
                'addon_id' => $request->addon_id,
                'start_date' => Carbon::createFromTimestamp($sub->current_period_start),
                'end_date' => Carbon::createFromTimestamp($sub->current_period_end),
                'stripe_subscription_id' => $sub->id,
                'stripe_subscription_status' => $sub->status,
                'coupon_code' => $request->coupon_applied ? $request->coupon_applied : null
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error',$e->getMessage());
        }

        $user->notify(new AddonReceipt($addon));

        return redirect()->route('profile/membership')->with('success','Your have been subscribed to the '.$addon->title.'.');
        // TODO what's next
        //return redirect()->route('profile/membership')
        //    ->with('success','Your have been subscribed to the '.$addon->title.'.')
        //    ->with('whatsnext','You didn\'t create any properties yet. Press "Add New Property" to create a new property.')
        //    ->with('gif', url('/').'/images/help/email-verified-create-property.gif');
    }

    public function addonCancel(Request $request) {
        $user = Auth::user();
        $addon = Addon::find($request->addon_id);
        $user_addon = UserAddon::where(['user_id' => $user->id, 'addon_id' => $addon->id])->first();

        if (!empty($user_addon)) {
            try {
                $this->subscriptionService->cancelSubscription($user_addon->stripe_subscription_id);
                $user_addon->delete();
            } catch (\Exception $e) {
                return back()->withInput()->with('error',$e->getMessage());
            }

            $user->notify(new CancelAddon($addon));
        }
        return redirect()->route('profile/membership')->with('success','Your have been unsubscribed from the '.$addon->title.'.');
    }

}
