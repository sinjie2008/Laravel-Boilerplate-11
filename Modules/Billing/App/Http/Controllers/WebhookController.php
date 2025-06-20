<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Billing\App\Jobs\HandleStripeWebhook;
use Modules\Billing\App\Models\WebhookLog;

class WebhookController extends Controller
{
    public function index(): View
    {
        $webhooks = WebhookLog::latest()->paginate();

        return view('billing::webhooks.index', compact('webhooks'));
    }

    public function replay(WebhookLog $webhook): RedirectResponse
    {
        HandleStripeWebhook::dispatch($webhook->payload);
        $webhook->update(['replayed_at' => now()]);
        activity()->performedOn($webhook)->causedBy(auth()->user())->log('replayed webhook');

        return back()->with('success', 'Webhook re-dispatched');
    }
}
