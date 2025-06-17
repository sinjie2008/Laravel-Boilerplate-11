<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->prefix('billing')
    ->group(function () {
        // API routes
    });
