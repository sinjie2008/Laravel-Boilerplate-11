<?php

declare(strict_types=1);

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'payload',
        'replayed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'replayed_at' => 'datetime',
    ];
}
