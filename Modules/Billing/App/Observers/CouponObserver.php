<?php

namespace Modules\Billing\App\Observers;

use Modules\Billing\App\Models\Coupon;

class CouponObserver
{
    public function created(Coupon $coupon): void
    {
        activity()->performedOn($coupon)->causedBy(auth()->user())->log('created coupon');
    }

    public function updated(Coupon $coupon): void
    {
        activity()->performedOn($coupon)->causedBy(auth()->user())->log('updated coupon');
    }

    public function deleted(Coupon $coupon): void
    {
        activity()->performedOn($coupon)->causedBy(auth()->user())->log('deleted coupon');
    }
}
