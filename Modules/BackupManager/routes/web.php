<?php

use Illuminate\Support\Facades\Route;
use Modules\BackupManager\App\Http\Controllers\BackupManagerController;

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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin'], function () { // Added admin prefix here
    Route::prefix('backup-manager')->name('backup-manager.')->group(function() {
        Route::get('/', [BackupManagerController::class, 'index'])->name('index');
        Route::post('/create', [BackupManagerController::class, 'create'])->name('create'); // Original Backup All (Files + DB) - Keep for compatibility or remove if desired
        Route::post('/create-files-backup', [BackupManagerController::class, 'createFilesBackup'])->name('create-files-backup'); // Backup Files Only
        Route::post('/create-all-in-one-backup', [BackupManagerController::class, 'createAllInOneBackup'])->name('create-all-in-one-backup'); // Backup Files + DB (New)
        Route::post('/restore-database/{fileName}', [BackupManagerController::class, 'restoreDatabase'])->name('restore-database'); // Added DB restore route
        Route::get('/download/{fileName}', [BackupManagerController::class, 'download'])->name('download');
        Route::delete('/delete/{fileName}', [BackupManagerController::class, 'destroy'])->name('destroy');
    });
});
