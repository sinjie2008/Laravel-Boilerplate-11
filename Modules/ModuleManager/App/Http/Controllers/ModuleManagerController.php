<?php

namespace Modules\ModuleManager\App\Http\Controllers;

use App\Http\Controllers\Controller; // Correct base controller namespace
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File; // Import File facade
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Nwidart\Modules\Facades\Module; // Import Module facade
use ZipArchive; // Import ZipArchive
use Symfony\Component\Process\Process; // Import Process component
use Symfony\Component\Process\Exception\ProcessFailedException; // Import Process exception

class ModuleManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = Module::all();
        // You might want to pass the statuses from modules_statuses.json as well
        // For simplicity, we'll rely on Module::isEnabled() for now.
        return view('modulemanager::index', compact('modules'));
    }

    /**
     * Upload, extract, and migrate a new module.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'module_zip' => 'required|file|mimes:zip',
        ]);

        $zipFile = $request->file('module_zip');
        $tempPath = $zipFile->store('temp_modules'); // Store in storage/app/temp_modules
        $fullTempPath = storage_path('app/' . $tempPath);
        $modulesPath = config('modules.paths.modules', base_path('Modules'));
        $moduleName = null;
        $targetModulePath = null; // Initialize target path
        $tempExtractPath = null; // Initialize temp extract path

        $zip = new ZipArchive;

        try {
            if ($zip->open($fullTempPath) === TRUE) {
                // Extract to a temporary location first to inspect
                $tempExtractPath = storage_path('app/temp_extract_' . time());
                File::ensureDirectoryExists($tempExtractPath);
                $zip->extractTo($tempExtractPath);
                $zip->close(); // Close zip immediately after extraction

                // --- Find module name ---
                // Look for module.json in the root or one level deep
                $moduleJsonPath = null;
                $moduleDirName = null; // The actual directory name inside the zip

                $rootJsonPath = $tempExtractPath . '/module.json';
                if (File::exists($rootJsonPath)) {
                    $moduleJsonPath = $rootJsonPath;
                    // Find the directory name containing module.json (could be root)
                    $itemsInTemp = File::directories($tempExtractPath);
                    if (count($itemsInTemp) === 1 && File::isDirectory($itemsInTemp[0])) {
                         // Assume the single directory is the module dir if module.json is at root
                         // This handles cases where zip contains ModuleName/module.json
                         // We need the *containing* directory name for moving later
                         $moduleDirName = basename($itemsInTemp[0]);
                    } else {
                        // If module.json is at the root, and there isn't a single subfolder,
                        // we might need to create the module folder based on the name in json
                        // Let's read the name first.
                    }
                } else {
                    // If not in root, check one level deeper (common structure: ModuleName/module.json)
                    $subDirs = File::directories($tempExtractPath);
                    if (count($subDirs) === 1) { // Expecting a single module directory inside
                        $potentialJsonPath = $subDirs[0] . '/module.json';
                        if (File::exists($potentialJsonPath)) {
                            $moduleJsonPath = $potentialJsonPath;
                            $moduleDirName = basename($subDirs[0]); // Get the name of the directory
                        }
                    }
                }


                if (!$moduleJsonPath) {
                    throw new \Exception("Could not find module.json in the zip archive (checked root and one level deep).");
                }

                $moduleConfig = json_decode(File::get($moduleJsonPath), true);
                if (!isset($moduleConfig['name'])) {
                    throw new \Exception("module.json does not contain a 'name' key.");
                }
                $moduleName = $moduleConfig['name']; // Name from json

                // If module.json was at the root, and we didn't find a single dir,
                // use the name from json as the directory name.
                if (is_null($moduleDirName) && $moduleJsonPath === $rootJsonPath) {
                    $moduleDirName = $moduleName;
                } elseif (is_null($moduleDirName)) {
                     throw new \Exception("Could not determine the module directory name inside the zip.");
                }
                // --- End Find module name ---


                // --- Move extracted files to Modules directory ---
                $targetModulePath = $modulesPath . '/' . $moduleName; // Target path uses name from json

                if (File::exists($targetModulePath)) {
                    throw new \Exception("Module directory [{$targetModulePath}] already exists. Uninstall first if you want to replace it.");
                }

                // Determine the source path (the directory containing module.json)
                $sourcePath = dirname($moduleJsonPath); // Get the directory containing module.json

                // If module.json was at the root, the source is the tempExtractPath itself
                // If it was in a subdir, sourcePath is tempExtractPath/ModuleName
                // We need to move the *contents* of sourcePath OR the sourcePath directory itself

                if ($moduleJsonPath === $rootJsonPath) {
                     // If module.json is at the root, we need to create the target dir first
                     // and move the *contents* of tempExtractPath into it.
                     File::ensureDirectoryExists($targetModulePath);
                     // Move all files and folders from tempExtractPath to targetModulePath
                     $itemsToMove = File::glob($tempExtractPath . '/*');
                     foreach ($itemsToMove as $item) {
                         File::move($item, $targetModulePath . '/' . basename($item));
                     }
                } else {
                    // If module.json was in a subdirectory, move the whole subdirectory
                    File::moveDirectory($sourcePath, $targetModulePath);
                }


                // Clean up temporary extraction directory (should be empty or non-existent now)
                if (File::isDirectory($tempExtractPath)) {
                    File::deleteDirectory($tempExtractPath);
                }


                // --- Run Migrations ---
                Log::info("Attempting to run migrations for module: {$moduleName}");
                Artisan::call('module:migrate', ['module' => $moduleName, '--force' => true]);
                Log::info("Migration output for {$moduleName}: " . Artisan::output()); // Log output

                // --- Enable Module ---
                 // Run composer dump-autoload first to ensure the new module is recognized
                try {
                    $process = new Process(['composer', 'dump-autoload'], base_path());
                    $process->mustRun();
                    Log::info("Composer dump-autoload executed successfully for {$moduleName}. Output: " . $process->getOutput());
                } catch (ProcessFailedException $exception) {
                    Log::error("Composer dump-autoload failed for {$moduleName}: " . $exception->getMessage());
                    // Decide if this should be a fatal error for the upload process
                    // throw new \Exception("Failed to run composer dump-autoload. Module might not be usable.");
                }

                $moduleInstance = Module::find($moduleName);
                if ($moduleInstance) {
                    Log::info("Module instance found for [{$moduleName}]. Enabling...");
                    $moduleInstance->enable();
                    Log::info("Module [{$moduleName}] enabled.");
                } else {
                     Log::warning("Could not find module instance for [{$moduleName}] even after dump-autoload. Module might not be registered correctly or there's a naming mismatch.");
                     // Don't throw an error, but log it. The module is extracted.
                }

                // --- Clear Cache ---
                Artisan::call('optimize:clear');
                Log::info("Cache cleared after enabling {$moduleName}.");

                // --- Clean up temporary zip file ---
                Storage::delete($tempPath);

                return redirect()->route('module-manager.index')->with('success', "Module [{$moduleName}] uploaded, extracted, migrated, and attempted to enable successfully.");

            } else {
                throw new \Exception("Failed to open the zip archive. Error code: " . $zip->status);
            }
        } catch (\Exception $e) {
            // Clean up on error
            if (isset($zip) && $zip instanceof ZipArchive && $zip->getStatusString()) { // Check if zip is open before trying to close
                 @$zip->close(); // Use @ to suppress errors if already closed
            }
            Storage::delete($tempPath); // Delete temp zip
            if (isset($tempExtractPath) && File::isDirectory($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath); // Delete temp extraction folder
            }
             // Optionally delete the target module folder if it was partially created
            if ($moduleName && isset($targetModulePath) && File::isDirectory($targetModulePath)) {
                 File::deleteDirectory($targetModulePath);
                 Log::info("Cleaned up partially created module directory: {$targetModulePath}");
            }

            Log::error("Module upload failed: " . $e->getMessage() . "\n" . $e->getTraceAsString()); // Log the error and stack trace
            return redirect()->route('module-manager.index')->with('error', "Failed to upload module: " . $e->getMessage());
        }
    }


    /**
     * Activate the specified module.
     */
    public function activate($module): RedirectResponse
    {
        $moduleInstance = Module::find($module);

        if (!$moduleInstance) {
            // Try composer dump-autoload before failing completely
            try {
                $process = new Process(['composer', 'dump-autoload'], base_path());
                $process->mustRun();
                Log::info("Composer dump-autoload executed successfully during activation check for {$module}. Output: " . $process->getOutput());
            } catch (ProcessFailedException $exception) {
                Log::error("Composer dump-autoload failed during activation check for {$module}: " . $exception->getMessage());
                // Proceed with the check anyway, but log the error
            }
            $moduleInstance = Module::find($module);
            if (!$moduleInstance) {
                return redirect()->route('module-manager.index')->with('error', "Module [{$module}] not found even after autoload dump.");
            }
        }


        try {
            $moduleInstance->enable();
            // Optionally clear cache after enabling
            Artisan::call('optimize:clear');
            return redirect()->route('module-manager.index')->with('success', "Module [{$module}] activated successfully.");
        } catch (\Exception $e) {
             Log::error("Failed to activate module [{$module}]: " . $e->getMessage());
            return redirect()->route('module-manager.index')->with('error', "Failed to activate module [{$module}]: " . $e->getMessage());
        }
    }

    /**
     * Deactivate the specified module.
     */
    public function deactivate($module): RedirectResponse
    {
        $moduleInstance = Module::find($module);

        if (!$moduleInstance) {
             return redirect()->route('module-manager.index')->with('error', "Module [{$module}] not found.");
        }

        // Prevent deactivating the ModuleManager itself (optional, but recommended)
        if (strtolower($module) === 'modulemanager') {
             return redirect()->route('module-manager.index')->with('error', 'Cannot deactivate the Module Manager itself.');
        }

        try {
            $moduleInstance->disable();
             // Optionally clear cache after disabling
            Artisan::call('optimize:clear');
            return redirect()->route('module-manager.index')->with('success', "Module [{$module}] deactivated successfully.");
        } catch (\Exception $e) {
             Log::error("Failed to deactivate module [{$module}]: " . $e->getMessage());
            return redirect()->route('module-manager.index')->with('error', "Failed to deactivate module [{$module}]: " . $e->getMessage());
        }
    }

     /**
     * Uninstall the specified module (Deletes files).
     * WARNING: This is a destructive action. Use with extreme caution.
     * Consider adding more checks or confirmations.
     */
    public function uninstall($module): RedirectResponse
    {
        $moduleInstance = Module::find($module);

        if (!$moduleInstance) {
            return redirect()->route('module-manager.index')->with('error', "Module [{$module}] not found.");
        }

        // Prevent uninstalling the ModuleManager itself
        if (strtolower($module) === 'modulemanager') {
             return redirect()->route('module-manager.index')->with('error', 'Cannot uninstall the Module Manager itself.');
        }

        try {
            // 1. Disable the module first (if enabled)
            if ($moduleInstance->isEnabled()) {
                $moduleInstance->disable();
                Log::info("Module [{$module}] disabled before uninstall.");
            }

            // 2. Delete the module files
            $modulePath = $moduleInstance->getPath();
            Log::info("Attempting to delete module directory: {$modulePath}");
            if (File::isDirectory($modulePath)) {
                if (File::deleteDirectory($modulePath)) {
                     Log::info("Successfully deleted module directory: {$modulePath}");
                } else {
                    Log::error("File::deleteDirectory failed for: {$modulePath}");
                    throw new \Exception("Failed to delete module directory automatically.");
                }
            } else {
                 Log::warning("Module directory not found at expected path: {$modulePath}. Skipping deletion.");
                 // Attempt module:delete command as a fallback if path wasn't found
                 // Check if module:delete command exists before calling
                 if (class_exists(\Nwidart\Modules\Commands\DeleteCommand::class)) {
                     Log::info("Attempting fallback: php artisan module:delete {$module}");
                     Artisan::call('module:delete', ['module' => $module, '--force' => true]);
                     Log::info("Fallback module:delete output: " . Artisan::output());
                 } else {
                     Log::warning("module:delete command not available.");
                 }
            }


            // 3. Clear cache
            Artisan::call('optimize:clear');
             Log::info("Cache cleared after uninstalling {$module}.");

            // 4. Update composer autoload (important!)
            try {
                $process = new Process(['composer', 'dump-autoload'], base_path());
                $process->mustRun();
                Log::info("Composer dump-autoload executed successfully after uninstalling {$module}. Output: " . $process->getOutput());
            } catch (ProcessFailedException $exception) {
                Log::error("Composer dump-autoload failed after uninstalling {$module}: " . $exception->getMessage());
                // Log the error, but the uninstall might still be considered successful if files are gone
            }


            return redirect()->route('module-manager.index')->with('success', "Module [{$module}] uninstalled successfully. Files deleted.");

        } catch (\Exception $e) {
            // Attempt to re-enable if deletion failed mid-way (might not always be possible)
            // $moduleInstance->enable(); // Consider the state if deletion fails
            Log::error("Module uninstall failed for [{$module}]: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('module-manager.index')->with('error', "Failed to uninstall module [{$module}]: " . $e->getMessage());
        }
    }
}
