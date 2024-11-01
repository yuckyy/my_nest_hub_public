<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Grids\CouponsGridInterface;
use App\Services\StripeSubscriptionService;

class CouponController extends Controller
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

    public function index(CouponsGridInterface $couponsGrid, Request $request)
    {
        $query = Coupon::query();

        if (!$request->has('sort_by') || $request->get('sort_by') == '') {
            $query->orderBy('created_at','desc');
        }

        return $couponsGrid
                    ->create(['query' => $query, 'request' => $request])
                    ->renderOn('admin.coupons');
    }

    public function add()
    {
        return view('admin.add-edit-coupon');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);

        return view('admin.add-edit-coupon',['coupon' => $coupon]);
    }

    public function save(Request $request)
    {
        $couponId = $request->coupon_id ? $request->coupon_id : null;
        $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:coupons,code,' . $couponId .',id,deleted_at,NULL',
            'discount' => 'required',
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'discount' => (float) str_replace(",","",$request->discount),
        ];

        $couponData = [
            'id' => $request->code,
            'name' => $request->name,
            'duration' => 'forever',
            'currency' => 'USD',
            'amount_off' => (float) str_replace(",","",$request->discount) * 100,
        ];

        if ($couponId) {
            $coupon = Coupon::findOrFail($couponId);
            try {
                $this->subscriptionService->updateCoupon($coupon->code,['name' => $request->name]);
                $coupon->update($data);
            } catch (\Exception $e) {
                return back()->withInput()->with('error',$e->getMessage());
            }
        } else {
            try {
                $this->subscriptionService->createCoupon($couponData);
                Coupon::create($data);
            } catch (\Exception $e) {
                return back()->withInput()->with('error',$e->getMessage());
            }
        }

        return redirect('admin/coupons')->with('success', 'Coupon was saved successfully.');
    }

    public function ajaxGetUsers(Request $request)
    {
        $coupon = Coupon::findOrFail($request->id);
        $userPlans = $coupon->userPlans;

        return response()->json([
            'view' => view('admin.partials._show-coupon-users',compact('userPlans'))->render()
        ],200);
    }

    public function delete($coupon_id)
    {
        $coupon = Coupon::findOrFail($coupon_id);
        try {
            $this->subscriptionService->deleteCoupon($coupon->code);
        } catch (\Exception $e) {}

        $coupon->delete();

        return back()->with('success', 'Coupon was removed successfully.');
    }
}
