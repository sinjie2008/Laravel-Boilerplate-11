<?php

declare(strict_types=1);

namespace Modules\SanctumMonitor\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tokenable_id',
        'tokenable_type',
        'name',
        'action',
        'ip_address',
        'user_id',
    ];
}
