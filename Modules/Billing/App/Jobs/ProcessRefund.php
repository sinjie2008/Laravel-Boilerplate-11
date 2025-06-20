<?php

declare(strict_types=1);

namespace Modules\Billing\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Billing\App\Mail\RefundIssued;
use Modules\Billing\App\Models\Invoice;
use Modules\Billing\App\Services\StripeService;

class ProcessRefund implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice, public ?float $amount = null) {}

    public function handle(StripeService $stripe): void
    {
        $stripe->refund($this->invoice->stripe_id, $this->amount);

        $this->invoice->status = 'refunded';
        $this->invoice->save();

        activity()->performedOn($this->invoice)->log('issued refund');

        Mail::to($this->invoice->user->email)->queue(new RefundIssued($this->invoice));
    }
}
