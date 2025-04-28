<?php

use Illuminate\Support\Facades\Route;
use Modules\ExcelImportExcel\App\Http\Controllers\ExcelImportExcelController;
use Modules\ExcelImportExcel\App\Http\Controllers\ExcelTemplateController;

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

Route::group([], function () {
    // You might keep or remove this depending on if you need the default resource controller
    Route::resource('excelimportexcel', ExcelImportExcelController::class)->names('excelimportexcel');
});

// Route to display the list of available templates
Route::get('/excel-templates', [ExcelTemplateController::class, 'index'])
    ->name('excelimportexcel.templates.index'); // This route might be redundant now if /excelimportexcel shows the list

// Route for downloading Excel templates (now includes module name)
Route::get('/excel-template/download/{moduleName}/{modelName}', [ExcelTemplateController::class, 'downloadTemplate'])
    ->name('excelimportexcel.template.download');
