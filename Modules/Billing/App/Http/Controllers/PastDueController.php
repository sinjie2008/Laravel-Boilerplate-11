<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Billing\App\Jobs\RetrySubscriptionPayment;
use Modules\Billing\App\Models\Subscription;

class PastDueController extends Controller
{
    public function index(): View
    {
        $subscriptions = Subscription::with('user', 'paymentRetries')
            ->where('stripe_status', 'past_due')
            ->paginate(15);

        return view('billing::past-due.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['user', 'paymentRetries' => function ($q) {
            $q->latest();
        }]);

        return view('billing::past-due.show', compact('subscription'));
    }

    public function retry(Subscription $subscription): RedirectResponse
    {
        RetrySubscriptionPayment::dispatch($subscription);

        return back()->with('success', 'Payment retry queued');
    }
}
