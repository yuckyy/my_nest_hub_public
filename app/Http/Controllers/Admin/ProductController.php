<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionOption;
use App\Models\PlanOption;
use App\Services\StripeSubscriptionService;

class ProductController extends Controller
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
        $plans = SubscriptionPlan::orderby('id')->get();
        $subscriptionOptions = SubscriptionOption::orderby('id')->get();
        return view(
            'admin.products',
            [
                'plans' => $plans,
                'subscriptionOptions' => $subscriptionOptions,
            ]
        );
    }

    // public function add()
    // {
    //     return view('admin.add-edit-product');
    // }

    public function edit($id)
    {
        $product = SubscriptionPlan::findOrFail($id);
        $options = SubscriptionOption::get();

        return view('admin.add-edit-product',['product' => $product,'options' => $options]);
    }

    public function save(Request $request)
    {
        // $productId = $request->product_id ? $request->product_id : null;
        $productId = $request->product_id;
        $request->validate([
            'name' => 'required|max:255',
            //'max_units' => 'required|numeric',
            //'price' => 'required',
        ]);

        $data = [
            'name' => $request->name,
            'max_units' => $request->max_units,
            'price' => (float) str_replace(",","",$request->price),
        ];

        if($data['price'] == 0){
            $data['price'] = null;
        }
        if($data['max_units'] == 0){
            $data['max_units'] = null;
        }

        $product = SubscriptionPlan::findOrFail($productId);
        $oldPrice = $product->price;

        if ($product->name != $request->name) {
            try {
                $this->subscriptionService->updateProduct($product->stripe_product_id,$request->name);
            } catch (\Exception $e) {}
        }
        $product->update($data);
        foreach ($request->options as $optId) {
            if (!$product->hasOption($optId)) {
                PlanOption::create([
                    'plan_id' => $product->id,
                    'option_id' => $optId
                ]);
            }
        }
        foreach (PlanOption::where('plan_id',$product->id)->get() as $option) {
            if (!in_array($option->option_id,$request->options)) {
                $option->delete();
            }
        }

        if(($oldPrice != $product->price) && $product->stripe_product_id){
            //update product price in Stripe with API

            $oldStripePlanId = $product->stripe_plan_id;

            $d = [
                 'amount' => $product->price*100,
                 'currency' => 'usd',
                 'interval' => 'month',
                 'product' => $product->stripe_product_id,
                 'trial_period_days' => 30
            ];
            $stripePlan = $this->subscriptionService->createPlan($d);
            //\Log::info("Plan Created: " . json_encode($stripePlan));

            $product->stripe_plan_id = $stripePlan->id;
            $product->save();

            $r = $this->subscriptionService->updatePlan($oldStripePlanId, ["active" => false]);
            //\Log::info("Plan Deactivated: " . json_encode($r));
        }


        return redirect('admin/products')->with('success', 'Product was saved successfully.');
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
