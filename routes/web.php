<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DocumentController;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\SqlGeneratorController;

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

    Route::resource('roles', App\Http\Controllers\RoleController::class)->middleware('permission:view role|create role|update role|delete role');
    Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy'])->middleware('permission:delete role');
    Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'addPermissionToRole'])->middleware('permission:update role');
    Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'givePermissionToRole'])->middleware('permission:update role');

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
    
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('admin.activity-logs.index')
        ->middleware('permission:view activity logs');

    Route::get('/sql-generator', [SqlGeneratorController::class, 'index'])
        ->name('sql.generator')
        ->middleware('permission:view sqlgenerator');
    Route::post('/sql-generator', [SqlGeneratorController::class, 'generate'])
        ->name('sql.generate')
        ->middleware('permission:view sqlgenerator');
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('admin.activity-logs.index')
        ->middleware('permission:view activity logs');
});

Route::get('/sql-generator', [SqlGeneratorController::class, 'index'])->name('sql.generator');
Route::post('/sql-generator', [SqlGeneratorController::class, 'generate'])->name('sql.generate');
