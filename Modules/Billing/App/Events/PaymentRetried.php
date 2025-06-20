<?php

declare(strict_types=1);

namespace Modules\Billing\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Billing\App\Models\PaymentRetry;

class PaymentRetried
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PaymentRetry $retry) {}
}
