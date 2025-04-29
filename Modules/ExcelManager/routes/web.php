<?php

use Illuminate\Support\Facades\Route;
use Modules\ExcelManager\App\Http\Controllers\ExcelManagerController;
use Modules\ExcelManager\App\Http\Controllers\ExcelTemplateController;

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

// Group all Excel Manager routes under admin/excel-manager prefix
Route::prefix('admin/excel-manager')->name('excelmanager.')->group(function () {
    // Resource controller - Assuming index now handles the main listing page at admin/excel-manager
    Route::resource('/', ExcelManagerController::class)->parameters(['' => 'excelmanager']); // Use '/' for the base path within the prefix

    // Route for downloading Excel templates
    Route::get('template/download/{moduleName}/{modelName}', [ExcelTemplateController::class, 'downloadTemplate'])
        ->name('template.download');

    // Route for downloading actual data
    Route::get('data/download/{moduleName}/{modelName}', [ExcelTemplateController::class, 'downloadData'])
        ->name('data.download');

    // Route to show the upload form page
    Route::get('upload/{moduleName}/{modelName}', [ExcelTemplateController::class, 'showUploadForm'])
        ->name('upload.form');

    // Route to handle the actual excel upload
    Route::post('upload/{moduleName}/{modelName}', [ExcelTemplateController::class, 'handleUpload'])
        ->name('upload.handle');

    // Commenting out the old index route as the resource controller index likely handles this now
    // Route::get('/excel-templates', [ExcelTemplateController::class, 'index'])
    //     ->name('templates.index');
});

// Remove or comment out the old standalone routes if they are now covered by the group above

// Route::get('/excel-templates', [ExcelTemplateController::class, 'index'])
//     ->name('excelimportexcel.templates.index'); // This route might be redundant now if /excelimportexcel shows the list

// Route::get('/excel-template/download/{moduleName}/{modelName}', [ExcelTemplateController::class, 'downloadTemplate'])
//     ->name('excelimportexcel.template.download');

// Route::get('/excel-data/download/{moduleName}/{modelName}', [ExcelTemplateController::class, 'downloadData'])
//     ->name('excelimportexcel.data.download');

// Route::get('/excel-upload/{moduleName}/{modelName}', [ExcelTemplateController::class, 'showUploadForm'])
//     ->name('excelimportexcel.upload.form');

// Route::post('/excel-upload/{moduleName}/{modelName}', [ExcelTemplateController::class, 'handleUpload'])
//     ->name('excelimportexcel.upload.store');
