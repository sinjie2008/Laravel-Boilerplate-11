<?php

namespace Modules\Billing\App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleStripeWebhook implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $payload) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // process webhook payload
    }
}
