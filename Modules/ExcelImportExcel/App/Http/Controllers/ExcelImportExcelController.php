<?php

namespace Modules\ExcelImportExcel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module; // Add Module facade
use Illuminate\Support\Facades\Schema; // Add Schema facade for DB checks

class ExcelImportExcelController extends Controller
{
    /**
     * Display a listing of models and available templates.
     */
    public function index()
    {
        $foundModels = [];

        // Scan Models in active Modules
        $modules = Module::allEnabled();
        foreach ($modules as $module) { // Ensure this loop starts correctly
            $moduleModelPath = $module->getPath() . '/App/Models';
            if (File::isDirectory($moduleModelPath)) {
                foreach (File::files($moduleModelPath) as $modelFile) {
                    $modelName = $modelFile->getFilenameWithoutExtension();
                    // Basic check to avoid non-model files like .gitkeep or traits
                    if (Str::endsWith($modelFile->getFilename(), '.php') && ctype_upper(substr($modelName, 0, 1))) {
                         $fullNamespace = "Modules\\{$module->getName()}\\App\\Models\\" . $modelName;
                         // Check if it's actually a class before adding
                         if (class_exists($fullNamespace)) {
                            $foundModels[$module->getName() . '::' . $modelName] = [
                                'name' => $modelName,
                                'module' => $module->getName(),
                                'namespace' => $fullNamespace
                            ];
                         }
                    }
                }
            }
        } // Ensure this loop closes correctly

        // Scan Models in app/Models
        $appModelPath = app_path('Models');
        if (File::isDirectory($appModelPath)) { // Ensure this conditional starts correctly
            foreach (File::files($appModelPath) as $modelFile) {
                $modelName = $modelFile->getFilenameWithoutExtension();
                 // Basic check to avoid non-model files
                if (Str::endsWith($modelFile->getFilename(), '.php') && ctype_upper(substr($modelName, 0, 1))) {
                    $appNamespace = app()->getNamespace();
                    $fullNamespace = $appNamespace . 'Models\\' . $modelName;

                    // Check if already added from a module scan (less likely but possible)
                    // And check if it's actually a class
                    if (!isset($foundModels['App::'.$modelName]) && class_exists($fullNamespace)) {
                        $foundModels['App::' . $modelName] = [
                            'name' => $modelName,
                            'module' => 'App', // Indicate it's from the main app
                            'namespace' => $fullNamespace
                        ];
                    }
                }
            }
        } // Ensure this conditional closes correctly

        // Now, check for corresponding Export classes for the found models
        $exportPath = base_path('Modules/ExcelImportExcel/App/Exports');
        $existingExports = [];
        if (File::isDirectory($exportPath)) {
            foreach (File::files($exportPath) as $file) {
                $filename = $file->getFilenameWithoutExtension();
                if (Str::endsWith($filename, 'Export')) {
                    $exportModelName = Str::beforeLast($filename, 'Export');
                    if (!empty($exportModelName)) {
                        $existingExports[] = $exportModelName;
                    }
                }
            }
        }

        // Add template status to found models
        foreach ($foundModels as $key => $modelData) {
            $foundModels[$key]['has_template'] = in_array($modelData['name'], $existingExports);
        }


        // Sort by key (Module::Model or App::Model) for consistent order
        ksort($foundModels);

        // Pass the list of found models (now including template status) to the view
        // Renamed variable passed to view for clarity, matching the view's expectation
        $modelsWithTemplates = $foundModels;
        return view('excelimportexcel::index', compact('modelsWithTemplates'));
    }

    // --- Other methods (create, store, show, edit, update, destroy) remain unchanged ---

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
