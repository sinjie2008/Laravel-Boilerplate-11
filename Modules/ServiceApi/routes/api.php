<?php

use Illuminate\Support\Facades\Route;
use Modules\ServiceApi\Http\Controllers\Api\V1\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    ->middleware([
        'auth:sanctum', // Provided by Laravel Sanctum
        'subscription.active', // Custom middleware from SubscriptionManager
        'subscription.ratelimit', // Custom middleware from SubscriptionManager
        // 'permission:access_service_api' // Example: A general permission to access this API, can be more granular
    ])
    ->name('api.v1.services.') // Route name prefix
    ->group(function () {
        Route::apiResource('services', ServiceController::class);
        // Example for specific permission middleware on a route:
        // Route::post('services', [ServiceController::class, 'store'])->middleware('permission:create_service');
        // However, for this setup, permissions are checked inside the controller methods.
    });
