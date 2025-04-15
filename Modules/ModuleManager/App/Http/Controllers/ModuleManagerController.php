<?php

namespace Modules\ModuleManager\App\Http\Controllers;

use App\Http\Controllers\Controller; // Correct base controller namespace
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File; // Import File facade
use Nwidart\Modules\Facades\Module; // Import Module facade

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
     * Activate the specified module.
     */
    public function activate($module): RedirectResponse
    {
        $moduleInstance = Module::find($module);

        if (!$moduleInstance) {
            return redirect()->route('module-manager.index')->with('error', 'Module not found.');
        }

        try {
            $moduleInstance->enable();
            // Optionally clear cache after enabling
            Artisan::call('optimize:clear');
            return redirect()->route('module-manager.index')->with('success', "Module [{$module}] activated successfully.");
        } catch (\Exception $e) {
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
            return redirect()->route('module-manager.index')->with('error', 'Module not found.');
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
            return redirect()->route('module-manager.index')->with('error', 'Module not found.');
        }

        // Prevent uninstalling the ModuleManager itself
        if (strtolower($module) === 'modulemanager') {
             return redirect()->route('module-manager.index')->with('error', 'Cannot uninstall the Module Manager itself.');
        }

        try {
            // 1. Disable the module first (if enabled)
            if ($moduleInstance->isEnabled()) {
                $moduleInstance->disable();
            }

            // 2. Delete the module files
            // Note: The delete() method might not exist or work as expected depending on the package version.
            // Manually deleting the directory is a more reliable approach.
            $modulePath = $moduleInstance->getPath();
            if (File::isDirectory($modulePath)) {
                File::deleteDirectory($modulePath);
            } else {
                 // Fallback or alternative method if getPath() isn't right
                 Artisan::call('module:delete', ['module' => $module, '--force' => true]); // Check if this command exists and works
            }


            // 3. Clear cache
            Artisan::call('optimize:clear');

            // 4. Update composer autoload (important!)
            Artisan::call('dump-autoload');


            return redirect()->route('module-manager.index')->with('success', "Module [{$module}] uninstalled successfully. Files deleted.");

        } catch (\Exception $e) {
            // Attempt to re-enable if deletion failed mid-way (might not always be possible)
            // $moduleInstance->enable(); // Consider the state if deletion fails
            return redirect()->route('module-manager.index')->with('error', "Failed to uninstall module [{$module}]: " . $e->getMessage());
        }
    }
}
