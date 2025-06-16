<?php

declare(strict_types=1);

namespace Modules\SanctumMonitor\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'route',
        'ip_address',
        'method',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
