<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Modules\Billing\App\Models\Coupon;
use Modules\Billing\App\Models\Invoice;
use Modules\Billing\App\Models\Plan;
use Modules\Billing\App\Models\Subscription;
use Spatie\Activitylog\Models\Activity;

class AuditLogController
{
    public function index(): View
    {
        $activities = Activity::query()
            ->whereIn('subject_type', [
                Plan::class,
                Coupon::class,
                Subscription::class,
                Invoice::class,
            ])
            ->latest()
            ->paginate();

        return view('billing::audit.index', compact('activities'));
    }
}
