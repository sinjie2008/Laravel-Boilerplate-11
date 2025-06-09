<?php

use Illuminate\Support\Facades\Route;
use Modules\Post\App\Http\Controllers\PostApiController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::apiResource('posts', PostApiController::class)->names('posts');
});
