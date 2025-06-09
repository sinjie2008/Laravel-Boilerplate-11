<?php

use Illuminate\Support\Facades\Route;
use Modules\Post\App\Http\Controllers\PostController;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('posts', PostController::class)->names('posts');
});
