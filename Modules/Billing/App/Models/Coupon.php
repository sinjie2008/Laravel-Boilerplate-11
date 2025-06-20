<?php

declare(strict_types=1);

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'stripe_id',
        'amount_off',
        'percent_off',
        'duration',
        'applies_to',
        'synced',
    ];
}
