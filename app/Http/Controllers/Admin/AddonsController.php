<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionOption;
use App\Models\PlanOption;
use App\Services\StripeSubscriptionService;

class AddonsController extends Controller
{
    private $subscriptionService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StripeSubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index(Request $request)
    {
        $addons = Addon::orderby('id')->get();
        return view(
            'admin.addons',
            [
                'addons' => $addons,
            ]
        );
    }

    // public function add()
    // {
    //     return view('admin.add-edit-product');
    // }

    public function edit($id)
    {
        $addon = Addon::findOrFail($id);

        return view('admin.add-edit-addon',['addon' => $addon,]);
    }

    public function save(Request $request)
    {
        $addonId = $request->addon_id;
        $request->validate([
            'title' => 'required|max:255',
            //'max_units' => 'required|numeric',
            //'price' => 'required',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'price' => (float) str_replace(",","",$request->price),
        ];

        if($data['price'] == 0){
            $data['price'] = null;
        }
        if($data['description'] == ''){
            $data['description'] = null;
        }

        $addon = Addon::findOrFail($addonId);
        $oldPrice = $addon->price;

        /*
        if ($addon->title != $request->title) {
            try {
                $this->subscriptionService->updateProduct($addon->stripe_product_id,$request->title);
            } catch (\Exception $e) {}
        }
        */
        $addon->update($data);

        if(($oldPrice != $addon->price) && $addon->stripe_product_id){
            //update product price in Stripe with API

            $oldStripePlanId = $addon->stripe_plan_id;

            $d = [
                 'amount' => $addon->price*100,
                 'currency' => 'usd',
                 'interval' => 'month',
                 'product' => $addon->stripe_product_id,
                 //'trial_period_days' => 30
            ];
            $stripePlan = $this->subscriptionService->createPlan($d);
            \Log::info("Addon Plan Created: " . json_encode($stripePlan));

            $addon->stripe_plan_id = $stripePlan->id;
            $addon->save();

            $r = $this->subscriptionService->updatePlan($oldStripePlanId, ["active" => false]);
            \Log::info("Addon Plan Deactivated: " . json_encode($r));
        }


        return redirect('admin/addons')->with('success', 'Addon was saved successfully.');
    }

    // public function delete($product_id)
    // {
    //     $product = SubscriptionPlan::findOrFail($product_id);
    //     $product->delete();
    //
    //     return back()->with('success', 'Product was removed successfully.');
    // }


    public function saveOption(Request $request)
    {
        if(empty($request->option_id)){
            $request->validate([
                'option_name' => 'required',
            ]);
            $option = new SubscriptionOption;
            $option->name = $request->option_name;
            $option->save();
            return redirect('admin/products')->with('success', 'Subscription option has been created');
        }

        if($request->option_delete == 'delete'){
            $optionId = $request->option_id;
            $option = SubscriptionOption::findOrFail($optionId);
            try{
                $option->delete();
            } catch(Exception $e){
            }
            return redirect('admin/products')->with('success', 'Subscription option name has been deleted');
        }

        $optionId = $request->option_id;
        $request->validate([
            'option_name' => 'required',
        ]);

        $option = SubscriptionOption::findOrFail($optionId);
        $option->name = $request->option_name;
        $option->save();

        return redirect('admin/products')->with('success', 'Subscription option name has been updated');
    }

}
