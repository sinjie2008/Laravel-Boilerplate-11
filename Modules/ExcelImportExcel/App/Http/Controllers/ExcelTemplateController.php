<?php

namespace Modules\ExcelImportExcel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\ExcelImportExcel\App\Exports\GenericModelExport; // Import generic export
use Illuminate\Support\Facades\Schema; // Import Schema facade
use Nwidart\Modules\Facades\Module; // Import Module facade

class ExcelTemplateController extends Controller
{
    /**
     * Download an Excel template for the given model.
     * Will use specific Export class if available, otherwise generates dynamically.
     *
     * @param string $moduleName The source module ('App' for main app)
     * @param string $modelName The model name
     * @return BinaryFileResponse|\Illuminate\Http\Response
     */
    public function downloadTemplate(string $moduleName, string $modelName): BinaryFileResponse|Response
    {
        $exportInstance = null;
        $headings = [];

        // 1. Try to find specific Export class
        $specificExportClassName = ucfirst($modelName) . 'Export';
        $specificExportClassPath = "Modules\\ExcelImportExcel\\App\\Exports\\" . $specificExportClassName;

        if (class_exists($specificExportClassPath)) {
            $exportInstance = new $specificExportClassPath;
        } else {
            // 2. Specific Export not found, generate dynamically
            $modelNamespace = '';
            if ($moduleName === 'App') {
                $appNamespace = app()->getNamespace();
                $modelNamespace = $appNamespace . 'Models\\' . $modelName;
            } else {
                $module = Module::find($moduleName);
                if ($module && $module->isEnabled()) {
                    $modelNamespace = "Modules\\{$module->getName()}\\App\\Models\\" . $modelName;
                }
            }

            if (empty($modelNamespace) || !class_exists($modelNamespace)) {
                abort(404, "Model class '{$modelNamespace}' not found for {$moduleName}::{$modelName}.");
            }

            // Instantiate model to get properties
            try {
                $modelInstance = new $modelNamespace();

                // Prefer $fillable attributes if defined
                $headings = $modelInstance->getFillable();

                // Fallback to database columns if $fillable is empty
                if (empty($headings) && method_exists($modelInstance, 'getTable')) {
                    $tableName = $modelInstance->getTable();
                    if (Schema::hasTable($tableName)) {
                        $columns = Schema::getColumnListing($tableName);
                        // Filter out common unwanted columns for templates
                        $headings = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);
                    }
                }

                // Final fallback if no columns found
                if (empty($headings)) {
                    $headings = ['Column1', 'Column2', 'Column3']; // Default placeholder
                }

            } catch (\Throwable $e) {
                // Handle cases where model instantiation fails or DB connection issues
                 abort(500, "Could not determine columns for model '{$modelName}'. Error: " . $e->getMessage());
            }

            // Use the generic exporter with determined headings
            $exportInstance = new GenericModelExport($headings);
        }

        $fileName = $modelName . '_template.xlsx'; // Or .csv, etc.
        return Excel::download($exportInstance, $fileName);
    }

    /**
     * Display a listing of available Excel templates (DEPRECATED/REMOVED - Logic moved to ExcelImportExcelController).
     * Kept here temporarily to avoid breaking existing code, but should be removed.
     */
    // public function index()
    // {
    //     // This logic is now in ExcelImportExcelController
    //     // You might want to redirect or remove this method entirely
    //     return redirect()->route('excelimportexcel.index'); // Example redirect
    // }

    // --- Other methods (create, store, show, edit, update, destroy) remain unchanged ---
    // ... (keep the other methods if they are needed for other purposes)

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('excelimportexcel::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('excelimportexcel::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('excelimportexcel::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
