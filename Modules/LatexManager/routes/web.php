<?php

use Illuminate\Support\Facades\Route;
use Modules\LatexManager\App\Http\Controllers\LatexManagerController;
use Modules\LatexManager\App\Http\Controllers\LatexItemController; // Import the new controller

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
    // Route to display the LaTeX editor
    Route::get('latex-editor', [LatexManagerController::class, 'index'])->name('latex.editor');
    // Route to handle the compilation of LaTeX code
    Route::post('latex-compile', [LatexManagerController::class, 'compile'])->name('latex.compile');
    // Route to serve the generated PDF (needed for the iframe)
    Route::get('latex-pdf', [LatexManagerController::class, 'servePdf'])->name('latex.pdf');
});

// Group for Latex Item CRUD under admin prefix
Route::middleware(['web', 'auth'])->prefix('admin/latex-manager')->name('admin.latex-manager.')->group(function () {
    // Route for compiling LaTeX preview within CRUD
    Route::post('compile', [LatexItemController::class, 'compilePreview'])->name('compile');

    // Resource routes for LatexItem CRUD
    Route::resource('', LatexItemController::class)->parameters(['' => 'latex_item']); // Use empty string for resource base, parameter name 'latex_item'
});
