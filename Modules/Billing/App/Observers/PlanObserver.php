<?php

namespace Modules\Billing\App\Observers;

use Modules\Billing\App\Models\Plan;

class PlanObserver
{
    public function created(Plan $plan): void
    {
        activity()->performedOn($plan)->causedBy(auth()->user())->log('created plan');
    }

    public function updated(Plan $plan): void
    {
        activity()->performedOn($plan)->causedBy(auth()->user())->log('updated plan');
    }

    public function deleted(Plan $plan): void
    {
        activity()->performedOn($plan)->causedBy(auth()->user())->log('deleted plan');
    }
}
