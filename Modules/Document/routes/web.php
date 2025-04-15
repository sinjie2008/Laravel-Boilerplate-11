<?php

use Illuminate\Support\Facades\Route;
use Modules\Document\App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Apply the 'admin' prefix and 'auth' middleware, similar to the original setup
Route::group(['middleware' => ['auth', 'web'], 'prefix' => 'admin'], function () {
    // Define the resource routes for 'documents' (plural)
    // Apply the necessary permission middleware
    // Use a specific name prefix for the module's routes
    Route::resource('documents', DocumentController::class)
         ->names('document.documents') // Use a descriptive name prefix like 'moduleName.resourceName'
         ->middleware('permission:view documents|create documents|update documents|delete documents');
});
