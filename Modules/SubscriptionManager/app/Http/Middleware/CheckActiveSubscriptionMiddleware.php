<?php

namespace Modules\SubscriptionManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\SubscriptionManager\Models\Subscription;
use Carbon\Carbon;

class CheckActiveSubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // This middleware should typically run after Sanctum's authentication.
            // If somehow user is not authenticated, deny access.
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();

        // Find an active subscription for the user.
        // A user might have multiple subscriptions (e.g., past, future, current).
        // We need one that is currently 'active' and its date range is valid.
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('starts_at') // Or starts_at is in the past
                      ->orWhere('starts_at', '<=', Carbon::now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at') // Or ends_at is in the future
                      ->orWhere('ends_at', '>=', Carbon::now());
            })
            ->orderBy('created_at', 'desc') // Get the latest active one if multiple somehow exist
            ->first();

        if (!$activeSubscription) {
            return response()->json(['message' => 'No active subscription found or subscription has expired.'], 403);
        }

        // Optionally, attach the subscription or plan to the request for later use in controllers/services
        $request->attributes->add(['activeSubscription' => $activeSubscription]);
        $request->attributes->add(['activePlan' => $activeSubscription->plan]);


        return $next($request);
    }
}
