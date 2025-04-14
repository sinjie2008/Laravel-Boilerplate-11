<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
// use App\Http\Controllers\ActivityLogController; // Removed as it's now in the module
use App\Http\Controllers\DocumentController;
use Spatie\Activitylog\Models\Activity;
// Remove SqlGeneratorController import as it's now in the module
// use App\Http\Controllers\SqlGeneratorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function() {
    Route::resource('permissions', App\Http\Controllers\PermissionController::class)->middleware('permission:view permission|create permission|update permission|delete permission');
    Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy'])->middleware('permission:delete permission');

    // Role routes moved to Modules/Role/routes/web.php

    Route::resource('users', App\Http\Controllers\UserController::class)->middleware('permission:view user|create user|update user|delete user');
    Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy'])->middleware('permission:delete user');
    
    Route::middleware(['auth'])->group(function () {
        Route::get('storage/{id}/{filename}', function ($id, $filename) {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($id);
            if ($media->file_name !== $filename) {
                abort(404);
            }
            return response()->file($media->getPath());
        })->name('media.download');

        Route::resource('documents', DocumentController::class)->middleware('permission:view documents|create documents|update documents|delete documents');
    });
    
    // Route::get('/activity-logs', [ActivityLogController::class, 'index']) // Removed, handled by module
    //     ->name('admin.activity-logs.index')
    //     ->middleware('permission:view activity logs');

    // Remove old SQL Generator routes from here
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::prefix('admin')->middleware(['auth'])->group(function () { // Removed redundant group
//     Route::get('/activity-logs', [ActivityLogController::class, 'index']) // Removed, handled by module
//         ->name('admin.activity-logs.index')
//         ->middleware('permission:view activity logs');
// });

// Remove old SQL Generator routes from here as well

Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    Route::resource('todolist', Modules\TodoList\App\Http\Controllers\TodoListController::class)->names('admin.todolist');
    // Module routes are typically registered within their own service providers
    // No need to add SqlGenerator routes here manually if the module provider handles it
});
