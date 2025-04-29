<?php

namespace Modules\ExcelManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\ExcelManager\App\Exports\GenericModelExport; // Updated import
use Modules\ExcelManager\App\Exports\GenericCollectionExport; // Updated import
use Illuminate\Support\Facades\Schema; // Import Schema facade
use Nwidart\Modules\Facades\Module; // Import Module facade
use Illuminate\View\View; // Import View
use Modules\ExcelManager\App\Imports\GenericModelImport; // Updated import
use Maatwebsite\Excel\Validators\ValidationException; // Import validation exception
use Illuminate\Support\Facades\Log; // For logging errors

class ExcelTemplateController extends Controller
{
    /**
     * Generate and download an Excel template for a given model.
     *
     * @param string $moduleName The name of the module (or 'App')
     * @param string $modelName The name of the model class
     * @return BinaryFileResponse|Response The Excel download response
     */
    public function downloadTemplate(string $moduleName, string $modelName): BinaryFileResponse|Response
    {
        // Check for specific export class
        $specificExportClassName = $modelName . 'Export';
        $specificExportClassPath = "Modules\\ExcelManager\\App\\Exports\\" . $specificExportClassName;

        // If model has a specific export class, use that
        if (class_exists($specificExportClassPath)) {
            try {
                $specificExport = new $specificExportClassPath();
                return Excel::download($specificExport, $modelName . '_template.xlsx');
            } catch (\Throwable $e) {
                abort(500, "Error creating template with model-specific exporter: " . $e->getMessage());
            }
        }

        // Otherwise use a generic approach based on fillable or DB columns
        if ($moduleName === 'App') {
            $appNamespace = app()->getNamespace();
            $modelNamespace = $appNamespace . 'Models\\' . $modelName;
        } else {
            $module = Module::find($moduleName);
            if ($module && $module->isEnabled()) {
                $modelNamespace = "Modules\\{$module->getName()}\\App\\Models\\" . $modelName;
            } else {
                $modelNamespace = "";
            }
        }

        if (empty($modelNamespace) || !class_exists($modelNamespace)) {
            abort(404, "Model class '{$modelNamespace}' not found for {$moduleName}::{$modelName}.");
        }

        try {
            $modelInstance = new $modelNamespace();

            // Get columns from $fillable or fall back to DB columns
            $headings = $modelInstance->getFillable();

            // If fillable is empty, try to get columns from DB
            if (empty($headings)) {
                $table = $modelInstance->getTable();
                $headings = Schema::getColumnListing($table);
                
                // Filter out some common columns you don't want in templates
                $headings = array_diff($headings, ['id', 'created_at', 'updated_at', 'deleted_at']);
            }

            // Generate a template with these headings but empty data
            $exportInstance = new GenericModelExport($headings);

            $fileName = $modelName . '_template.xlsx';
            return Excel::download($exportInstance, $fileName);

        } catch (\Throwable $e) {
            abort(500, "Could not generate template for model '{$modelName}'. Error: " . $e->getMessage());
        }
    }

    /**
     * Download actual model data as Excel.
     *
     * @param string $moduleName The name of the module (or 'App')
     * @param string $modelName The name of the model class
     * @return BinaryFileResponse The Excel download response
     */
    public function downloadData(string $moduleName, string $modelName): BinaryFileResponse
    {
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

        // Fetch all data for the model
        try {
            $data = $modelNamespace::all(); // Fetch all records
        } catch (\Throwable $e) {
            abort(500, "Could not fetch data for model '{$modelName}'. Error: " . $e->getMessage());
        }

        // Use a generic exporter for collections
        // You might enhance this to determine headings dynamically if needed
        $exportInstance = new GenericCollectionExport($data);

        $fileName = $modelName . '_data_' . date('YmdHis') . '.xlsx';
        return Excel::download($exportInstance, $fileName);

    }

    /**
     * Display a listing of available Excel templates (DEPRECATED/REMOVED - Logic moved to ExcelManagerController).
     * Kept here temporarily to avoid breaking existing code, but should be removed.
     */
    // public function index()
    // {
    //     // This logic is now in ExcelManagerController
    //     // You might want to redirect or remove this method entirely
    //     return redirect()->route('excelmanager.index'); // Updated route name
    // }

    // --- Other methods (create, store, show, edit, update, destroy) remain unchanged ---
    // ... (keep the other methods if they are needed for other purposes)

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('excelmanager::create');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('excelmanager::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('excelmanager::edit');
    }

    /**
     * Show the upload form for a model's Excel file.
     *
     * @param string $moduleName The name of the module (or 'App')
     * @param string $modelName The name of the model class
     * @return View The view for the upload form
     */
    public function showUploadForm(string $moduleName, string $modelName): View
    {
        // Verify model exists
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

        return view('excelmanager::upload-form', compact('moduleName', 'modelName'));
    }

    /**
     * Handle the Excel upload and import.
     *
     * @param Request $request The HTTP request
     * @param string $moduleName The name of the module (or 'App')
     * @param string $modelName The name of the model class
     * @return RedirectResponse Redirect back with status message
     */
    public function handleUpload(Request $request, string $moduleName, string $modelName)
    {
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv'
                // 'max:10240' // Optional: 10MB max
            ],
        ]);

        $file = $request->file('excel_file');
        $statusMessage = "Excel file for {$modelName} processed.";
        $statusType = 'success'; // Default status type

        try {
            // Instantiate the generic importer with module and model names
            $importer = new GenericModelImport($moduleName, $modelName);

            // Import the data
            Excel::import($importer, $file);

            // If successful, set a success message
            // You could potentially get counts from the importer if needed
            // $rowCount = $importer->getRowCount(); // Example if you add row counting
            $statusMessage = "Excel file for {$modelName} imported successfully.";

        } catch (ValidationException $e) {
            $failures = $e->failures(); // Get validation failures
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row: " . $failure->row() . " - Attribute: '" . $failure->attribute() . "' - Error: " . implode(', ', $failure->errors());
            }
            Log::error("Validation errors during Excel import for {$moduleName}::{$modelName}: " . implode("; ", $errorMessages));
            $statusMessage = "Import failed due to validation errors. Please check the file and try again. Details: " . implode('; ', $errorMessages);
            $statusType = 'danger'; // Use error styling

            // Redirect back to the upload form with errors
            return redirect()->back()
                 ->withErrors($errorMessages) // Pass errors to the view
                 ->withInput(); // Keep old input (like file selection if possible)

        } catch (\InvalidArgumentException $e) {
            // Catch specific errors like Model not found from the importer constructor
            Log::error("Error during Excel import setup for {$moduleName}::{$modelName}: " . $e->getMessage());
            $statusMessage = "Import setup failed: " . $e->getMessage();
            $statusType = 'danger';

        } catch (\Throwable $e) {
            // Catch any other unexpected errors during import
            Log::error("Unexpected error during Excel import for {$moduleName}::{$modelName}: " . $e->getMessage());
            // Provide a generic error message to the user
            $statusMessage = "An unexpected error occurred during the import process. Please check the logs or contact support.";
            $statusType = 'danger';
        }

        // Redirect back with a status message (success or general failure)
        // Adjust the redirect target if needed (e.g., back to form or to an index page)
        // Using route helper for consistency
        $redirectRoute = route('excelmanager.upload.form', ['moduleName' => $moduleName, 'modelName' => $modelName]);

        return redirect($redirectRoute)
            ->with($statusType, $statusMessage);
    }
}
