<?php

declare(strict_types=1);

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stripe_id',
        'amount',
        'billing_interval',
        'status',
        'synced',
    ];
}
