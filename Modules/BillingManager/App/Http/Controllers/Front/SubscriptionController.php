<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Facades\Cashier;

class SubscriptionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Subscription creation logic placeholder
        return redirect()->route('billing.index');
    }

    public function cancel(): RedirectResponse
    {
        // Subscription cancel logic placeholder
        return redirect()->route('billing.index');
    }
}
