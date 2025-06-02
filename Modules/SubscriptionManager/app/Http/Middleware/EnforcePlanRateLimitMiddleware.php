<?php

namespace Modules\SubscriptionManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response; // Added for Response class
use Modules\SubscriptionManager\Models\Plan; // Added

class EnforcePlanRateLimitMiddleware
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            // Should be caught by previous auth middleware, but as a safeguard
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        /** @var Plan|null $activePlan */
        $activePlan = $request->attributes->get('activePlan');

        if (!$activePlan || $activePlan->api_call_limit_per_day <= 0) {
            // No plan attached, or plan has unlimited calls (or misconfigured)
            // Depending on policy, you might allow or deny. Here, we allow.
            return $next($request);
        }

        $limit = $activePlan->api_call_limit_per_day;
        $key = 'api_rate_limit:' . $user->id . ':' . today()->toDateString(); // Daily limit key

        if ($this->limiter->tooManyAttempts($key, $limit)) {
            $retryAfter = $this->limiter->availableIn($key);
            return response()->json([
                'message' => 'API rate limit exceeded for your current plan.',
                'retry_after_seconds' => $retryAfter,
            ], Response::HTTP_TOO_MANY_REQUESTS, [ // Use Response constants
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $limit,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        $this->limiter->hit($key, 86400); // Increment count, decay in 24 hours (86400 seconds)

        $response = $next($request);

        // Add rate limit headers to the response
        if ($response instanceof \Illuminate\Http\Response || $response instanceof \Illuminate\Http\JsonResponse) {
            $response->headers->set('X-RateLimit-Limit', $limit);
            $response->headers->set('X-RateLimit-Remaining', $this->limiter->retriesLeft($key, $limit));
        }
        
        return $response;
    }
}
