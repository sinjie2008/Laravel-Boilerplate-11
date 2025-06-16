<?php

declare(strict_types=1);

namespace Modules\SanctumMonitor\App\Observers;

use Illuminate\Support\Facades\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\SanctumMonitor\App\Models\TokenAuditLog;

class PersonalAccessTokenObserver
{
    public function created(PersonalAccessToken $token): void
    {
        TokenAuditLog::create([
            'tokenable_id' => $token->tokenable_id,
            'tokenable_type' => $token->tokenable_type,
            'name' => $token->name,
            'action' => 'created',
            'ip_address' => Request::ip(),
            'user_id' => auth()->id(),
        ]);
    }

    public function deleted(PersonalAccessToken $token): void
    {
        TokenAuditLog::create([
            'tokenable_id' => $token->tokenable_id,
            'tokenable_type' => $token->tokenable_type,
            'name' => $token->name,
            'action' => 'deleted',
            'ip_address' => Request::ip(),
            'user_id' => auth()->id(),
        ]);
    }
}
