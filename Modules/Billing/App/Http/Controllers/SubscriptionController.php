<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Billing\App\Models\Plan;
use Modules\Billing\App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $query = Subscription::with(['user', 'plan']);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($plan = $request->string('plan')->toString()) {
            $query->where('stripe_price', $plan);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('stripe_status', $status);
        }

        /** @var LengthAwarePaginator $subscriptions */
        $subscriptions = $query->paginate(15)->withQueryString();
        $plans = Plan::pluck('name', 'stripe_id');
        $statuses = Subscription::query()->distinct()->pluck('stripe_status');

        return view('billing::subscriptions.index', compact('subscriptions', 'plans', 'statuses'));
    }

    public function show(Subscription $subscription): \Illuminate\View\View
    {
        $subscription->load(['user', 'plan']);

        return view('billing::subscriptions.show', compact('subscription'));
    }
}
