<?php

declare(strict_types=1);

namespace Modules\SanctumMonitor\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\SanctumMonitor\App\Models\ApiActivity;

class LogSanctumActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('sanctummonitor.enable_logging') && $request->user()) {
            ApiActivity::create([
                'user_id' => $request->user()->id,
                'route' => $request->path(),
                'ip_address' => $request->ip(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}
