<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Role\App\Http\Controllers\Auth\ApiTokenController;

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

Route::post('v1/login', [ApiTokenController::class, 'login'])->name('api.login');

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::post('logout', [ApiTokenController::class, 'logout'])->name('logout');
    Route::get('role', fn (Request $request) => $request->user())->name('role');
});
