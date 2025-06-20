<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Billing\App\Http\Requests\StoreCouponRequest;
use Modules\Billing\App\Http\Requests\UpdateCouponRequest;
use Modules\Billing\App\Models\Coupon;
use Modules\Billing\App\Services\StripeService;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::all();

        return view('billing::coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('billing::coupons.create');
    }

    public function store(StoreCouponRequest $request, StripeService $stripe): RedirectResponse
    {
        $data = $request->validated();
        try {
            $stripeId = $stripe->createCoupon($data);
            $data['stripe_id'] = $stripeId;
            $data['synced'] = true;
        } catch (\Exception $e) {
            $data['synced'] = false;
        }
        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created');
    }

    public function edit(Coupon $coupon)
    {
        return view('billing::coupons.edit', compact('coupon'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon, StripeService $stripe): RedirectResponse
    {
        $data = $request->validated();
        try {
            $stripe->updateCoupon($coupon, $data);
            $data['synced'] = true;
        } catch (\Exception $e) {
            $data['synced'] = false;
        }
        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated');
    }

    public function destroy(Coupon $coupon, StripeService $stripe): RedirectResponse
    {
        try {
            $stripe->deleteCoupon($coupon);
        } catch (\Exception $e) {
            // ignore
        }
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted');
    }
}
