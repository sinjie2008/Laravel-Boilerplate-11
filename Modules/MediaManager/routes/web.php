<?php

use Illuminate\Support\Facades\Route;
use Modules\MediaManager\App\Http\Controllers\MediaManagerController;

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

// Group routes under a common prefix and middleware (optional, but good practice)
// Consider adding auth middleware here
Route::prefix('media-manager')
    // ->middleware('auth') 
    ->name('mediamanager.') // Route name prefix
    ->group(function () {
    
    // Use resource controller for standard CRUD routes
    // This provides mediamanager.index, mediamanager.create, mediamanager.store, etc.
    Route::get('/', [MediaManagerController::class, 'index'])->name('index');
    Route::get('/create', [MediaManagerController::class, 'create'])->name('create');
    Route::post('/', [MediaManagerController::class, 'store'])->name('store');
    
    // Add routes for show (download), edit, update, destroy
    Route::get('/{media}', [MediaManagerController::class, 'show'])->name('show'); // For download
    Route::get('/{media}/edit', [MediaManagerController::class, 'edit'])->name('edit');
    Route::put('/{media}', [MediaManagerController::class, 'update'])->name('update');
    Route::delete('/{media}', [MediaManagerController::class, 'destroy'])->name('destroy');

});
