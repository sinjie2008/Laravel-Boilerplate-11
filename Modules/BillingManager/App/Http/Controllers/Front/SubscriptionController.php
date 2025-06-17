<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\BillingManager\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subscriptions)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'price' => 'required|string',
            'payment_method' => 'nullable|string',
        ]);

        $this->subscriptions->subscribe(
            $request->user(),
            $request->string('price')->toString(),
            $request->string('payment_method')->toString()
        );

        return redirect()->route('billing.index');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $this->subscriptions->cancel($request->user());

        return redirect()->route('billing.index');
    }
}
