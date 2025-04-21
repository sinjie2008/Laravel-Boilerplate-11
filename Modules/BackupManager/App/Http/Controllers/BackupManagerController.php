<?php

namespace Modules\BackupManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB; // Added for database operations
use Illuminate\Support\Facades\File; // Added for file operations
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// Removed Symfony Process as we'll use DB::unprepared
// use Symfony\Component\Process\Process;
// use Symfony\Component\Process\Exception\ProcessFailedException;
use Exception;
use ZipArchive; // Added for zip extraction

class BackupManagerController extends Controller
{
    private $disk;
    private $backupName;

    public function __construct()
    {
        // Ensure user has permission to access this module
        // $this->middleware('permission:backup-manager-access'); // Example permission check

        $this->disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $this->backupName = config('backup.backup.name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $backups = [];
        try {
            $files = $this->disk->files($this->backupName);
            $backups = collect($files)
                ->filter(function ($file) {
                    return pathinfo($file, PATHINFO_EXTENSION) === 'zip';
                })
                ->map(function ($file) {
                    return [
                        'file_path' => $file,
                        'file_name' => basename($file),
                        'file_size' => $this->formatBytes($this->disk->size($file)),
                        'last_modified' => date('Y-m-d H:i:s', $this->disk->lastModified($file)),
                    ];
                })
                ->reverse()
                ->values(); // Reset keys after reversing
        } catch (Exception $e) {
            Log::error("Error listing backups: " . $e->getMessage());
            // Optionally flash an error message to the session
            session()->flash('error', 'Could not list backups. Please check storage configuration and permissions.');
        }

        return view('backupmanager::index', compact('backups'));
    }

    /**
     * Trigger the backup creation process.
     */
    public function create(): RedirectResponse
    {
        try {
            // Queue the backup job for better performance
            Artisan::queue('backup:run', ['--only-files' => false, '--only-db' => false]); // Adjust flags as needed
            session()->flash('success', 'Backup creation process started. It may take a few minutes to complete.');
        } catch (Exception $e) {
            Log::error("Error starting backup creation: " . $e->getMessage());
            session()->flash('error', 'Could not start backup creation: ' . $e->getMessage());
        }

        return redirect()->route('backup-manager.index');
    }

    /**
     * Trigger the file-only backup creation process.
     */
    public function createFilesBackup(): RedirectResponse
    {
        try {
            // Queue the backup job for files only
            Artisan::queue('backup:run', ['--only-files' => true, '--only-db' => false]);
            session()->flash('success', 'File backup creation process started. It may take a few minutes to complete.');
        } catch (Exception $e) {
            Log::error("Error starting file backup creation: " . $e->getMessage());
            session()->flash('error', 'Could not start file backup creation: ' . $e->getMessage());
        }

        return redirect()->route('backup-manager.index');
    }

    /**
     * Trigger the all-in-one (files + database) backup creation process.
     */
    public function createAllInOneBackup(): RedirectResponse
    {
        try {
            // Queue the backup job for both files and database
            // The default behavior of backup:run is to back up everything specified
            // in the config, so explicitly setting both to false is equivalent to no flags.
            Artisan::queue('backup:run', ['--only-files' => false, '--only-db' => false]);
            session()->flash('success', 'All-In-One backup creation process started. It may take a few minutes to complete.');
        } catch (Exception $e) {
            Log::error("Error starting All-In-One backup creation: " . $e->getMessage());
            session()->flash('error', 'Could not start All-In-One backup creation: ' . $e->getMessage());
        }

        return redirect()->route('backup-manager.index');
    }

    /**
     * Download a specific backup file.
     */
    public function download($fileName)
    {
        $filePath = $this->backupName . '/' . $fileName;

        if (!$this->disk->exists($filePath)) {
            abort(404, "Backup file not found.");
        }

        try {
            return $this->disk->download($filePath);
        } catch (Exception $e) {
            Log::error("Error downloading backup {$fileName}: " . $e->getMessage());
            session()->flash('error', 'Could not download backup file: ' . $e->getMessage());
            return redirect()->route('backup-manager.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($fileName): RedirectResponse
    {
        $filePath = $this->backupName . '/' . $fileName;

        if (!$this->disk->exists($filePath)) {
            session()->flash('error', 'Backup file not found.');
            return redirect()->route('backup-manager.index');
        }

        try {
            $this->disk->delete($filePath);
            session()->flash('success', 'Backup file deleted successfully.');
        } catch (Exception $e) {
            Log::error("Error deleting backup {$fileName}: " . $e->getMessage());
            session()->flash('error', 'Could not delete backup file: ' . $e->getMessage());
        }

        return redirect()->route('backup-manager.index');
    }

    /**
     * Format bytes to human-readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // Restore function (Placeholder - requires more complex implementation)
    /*
    public function restore($fileName): RedirectResponse
    {
        // Implementation for restoring backup needs careful consideration
        // - Download the backup
        // - Unzip it
        // - Restore database (e.g., using mysql command)
        // - Restore files
        // This is complex and potentially risky, often better done manually or via CLI.
        session()->flash('info', 'Restore functionality is not yet implemented.');
        return redirect()->route('backup-manager.index');
    }
    */

    /**
     * Restore the database from a specific backup file.
     * WARNING: This overwrites the current database.
     */
    public function restoreDatabase($fileName): RedirectResponse
    {
        $filePath = $this->backupName . '/' . $fileName;
        $tempDir = storage_path('app/backup-restore-temp');
        $sqlDumpPath = null;

        Log::info("Attempting database restore from: {$fileName}");

        // 1. Check if backup file exists
        if (!$this->disk->exists($filePath)) {
            session()->flash('error', 'Backup file not found.');
            Log::error("Restore failed: Backup file {$fileName} not found on disk {$this->disk->getConfig()['driver']}.");
            return redirect()->route('backup-manager.index');
        }

        try {
            // 2. Create temporary directory
            if (!File::isDirectory($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
                Log::info("Created temporary directory: {$tempDir}");
            }

            // 3. Extract the backup zip file
            $zipPath = $this->disk->path($filePath); // Get absolute path
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempDir);
                $zip->close();
                Log::info("Successfully extracted {$fileName} to {$tempDir}");
            } else {
                throw new Exception("Failed to open zip archive: {$fileName}");
            }

            // 4. Find the SQL dump file (usually in db-dumps)
            $dumpDir = $tempDir . '/db-dumps';
            if (!File::isDirectory($dumpDir)) {
                 // Check root if not in db-dumps (older backup versions?)
                 $dumpDir = $tempDir;
            }

            $sqlFiles = File::glob($dumpDir . '/*.sql');
            if (empty($sqlFiles)) {
                throw new Exception("No SQL dump file found in the extracted backup.");
            }
            $sqlDumpPath = $sqlFiles[0]; // Use the first SQL file found
            Log::info("Found SQL dump file: {$sqlDumpPath}");

            // 5. Read SQL content
            $sqlContent = File::get($sqlDumpPath);
            if ($sqlContent === false) {
                throw new Exception("Failed to read SQL dump file content from: {$sqlDumpPath}");
            }

            // 6. Execute SQL using DB::unprepared
            Log::info("Executing SQL dump using DB::unprepared...");
            DB::connection()->unprepared($sqlContent); // Use the default connection

            Log::info("Database restore via DB::unprepared completed successfully for {$fileName}.");
            session()->flash('success', 'Database restored successfully from ' . $fileName);

        } catch (Exception $e) { // Catch general exceptions, including DB errors
            Log::error("An error occurred during database restore for {$fileName}: " . $e->getMessage());
            session()->flash('error', 'An error occurred during restore: ' . $e->getMessage());
        } finally {
            // 7. Clean up temporary directory
            if (File::isDirectory($tempDir)) {
                File::deleteDirectory($tempDir);
                Log::info("Cleaned up temporary directory: {$tempDir}");
            }
        }

        return redirect()->route('backup-manager.index');
    }
}
