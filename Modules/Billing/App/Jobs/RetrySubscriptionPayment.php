<?php

declare(strict_types=1);

namespace Modules\Billing\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Billing\App\Events\PaymentRetried;
use Modules\Billing\App\Models\PaymentRetry;
use Modules\Billing\App\Models\Subscription;
use Modules\Billing\App\Services\StripeService;

class RetrySubscriptionPayment implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function handle(StripeService $stripe): void
    {
        $status = 'success';
        $reason = null;

        try {
            $stripe->retrySubscriptionPayment($this->subscription);
        } catch (\Exception $e) {
            $status = 'failed';
            $reason = $e->getMessage();
        }

        $retry = PaymentRetry::create([
            'subscription_id' => $this->subscription->id,
            'status' => $status,
            'failure_reason' => $reason,
        ]);

        PaymentRetried::dispatch($retry);
    }
}
