<?php

namespace Modules\LatexManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Process; // Add Process facade
use Illuminate\Support\Facades\Storage; // Add Storage facade
use Illuminate\Support\Str;             // Add Str facade
use Modules\LatexManager\App\Models\LatexItem; // Import the model
use Modules\LatexManager\App\Models\LatexManagerSetting; // Import the settings model

class LatexItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $latexItems = LatexItem::all(); // Fetch all items
        return view('latexmanager::index', compact('latexItems')); // Pass data to the view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('latexmanager::create'); // Point to the correct view
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'latex_editor_hidden' => 'nullable|string', // Validate the hidden field
        ]);

        // Map hidden field to the correct database column
        $createData = $validatedData;
        $createData['latex_editor'] = $validatedData['latex_editor_hidden'] ?? null;
        unset($createData['latex_editor_hidden']); // Remove the temporary field

        LatexItem::create($createData);

        // Optionally, add a success flash message
        // session()->flash('success', 'Latex item created successfully.');

        return redirect()->route('admin.latex-manager.index')
                         ->with('success', 'Latex item created successfully.'); // Add success message
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $latexItem = LatexItem::findOrFail($id); // Find item or fail
        return view('latexmanager::show', compact('latexItem')); // Pass item to view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $latexItem = LatexItem::findOrFail($id); // Find item or fail
        return view('latexmanager::edit', compact('latexItem')); // Pass item to edit view
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'latex_editor_hidden' => 'nullable|string', // Validate the hidden field
        ]);

        // Map hidden field to the correct database column
        $updateData = $validatedData;
        $updateData['latex_editor'] = $validatedData['latex_editor_hidden'] ?? null;
        unset($updateData['latex_editor_hidden']); // Remove the temporary field

        $latexItem = LatexItem::findOrFail($id);
        $latexItem->update($updateData);

        return redirect()->route('admin.latex-manager.index')
                         ->with('success', 'Latex item updated successfully.'); // Add success message
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse // Add return type hint
    {
        $latexItem = LatexItem::findOrFail($id);
        $latexItem->delete();

        return redirect()->route('admin.latex-manager.index')
                          ->with('success', 'Latex item deleted successfully.'); // Add success message
    }

    /**
     * Show the form for configuring LatexManager settings.
     */
    public function showConfigForm()
    {
        // Fetch the first settings record (assuming only one)
        $settings = LatexManagerSetting::first();
        return view('latexmanager::config', compact('settings'));
    }

    /**
     * Store the LatexManager settings.
     */
    public function storeConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pdflatex_path' => 'nullable|string', // Path can be nullable or empty
        ]);

        // Update or create the settings record (assuming ID 1 or first record)
        LatexManagerSetting::updateOrCreate(
            ['id' => 1], // Simple way to ensure only one record
            ['pdflatex_path' => $validated['pdflatex_path']]
        );

        return redirect()->route('admin.latex-manager.config.show')
                         ->with('success', 'Configuration updated successfully.');
    }

    /**
     * Helper method to get the configured pdflatex path.
     *
     * @return string
     */
    public function getPdfLatexPath(): string
    {
        $settings = LatexManagerSetting::first();
        $path = $settings->pdflatex_path ?? 'pdflatex'; // Default to 'pdflatex' if not set

        // Simple quoting for paths with spaces, might need refinement based on OS/shell
        if (str_contains($path, ' ') && !str_starts_with($path, '"')) {
            $path = '"' . $path . '"';
        }

        return $path;
    }

    /**
     * Compile LaTeX code for preview.
     */
    public function compilePreview(Request $request)
    {
        $request->validate(['content' => 'required|string']);
        $latexContent = $request->input('content');

        // Generate unique names for temporary files
        $uniqueId = Str::uuid();
        $tempDirName = "latex_compile_{$uniqueId}";
        $texFileName = "document.tex";
        $pdfFileName = "document.pdf";
        $logFileName = "document.log";

        // Use a temporary directory within storage/app
        $tempPath = storage_path("app/temp_latex/{$tempDirName}");
        if (!Storage::disk('local')->makeDirectory("temp_latex/{$tempDirName}")) {
             return response()->json(['success' => false, 'message' => 'Could not create temporary directory.'], 500);
        }

        // Write the .tex file
        Storage::disk('local')->put("temp_latex/{$tempDirName}/{$texFileName}", $latexContent);

        // Define the full path to pdflatex using the helper method
        $pdflatexPath = $this->getPdfLatexPath();

        // Construct the command string
        // Note: Using the full path now.
        // -interaction=nonstopmode prevents pdflatex from stopping on errors
        // -output-directory ensures output files stay in our temp dir
        $command = "{$pdflatexPath} -interaction=nonstopmode -output-directory=\"{$tempPath}\" \"{$texFileName}\"";

        // Run the command using Process facade
        $process = Process::path($tempPath)->run($command);

        if ($process->successful()) {
            $pdfPathRelative = "temp_latex/{$tempDirName}/{$pdfFileName}";

            // Check if PDF exists before proceeding
            if (Storage::disk('local')->exists($pdfPathRelative)) {
                 // Move the PDF to a publicly accessible temporary location
                 $publicTempDir = 'public/latex_previews';
                 $publicPdfName = "preview_{$uniqueId}.pdf";
                 $publicPdfPathRelative = "{$publicTempDir}/{$publicPdfName}";

                 if (!Storage::disk('local')->exists($publicTempDir)) {
                     Storage::disk('local')->makeDirectory($publicTempDir);
                 }

                 // Copy the file from local storage to public storage
                 Storage::put($publicPdfPathRelative, Storage::disk('local')->get($pdfPathRelative));

                 // Generate a URL to the public file
                 $pdfUrl = Storage::url($publicPdfPathRelative);

                 // Clean up the compilation directory
                 Storage::disk('local')->deleteDirectory("temp_latex/{$tempDirName}");

                 return response()->json(['success' => true, 'pdf_url' => $pdfUrl]);
            } else {
                 // PDF not found, likely compilation error despite exit code 0
                 $logContent = Storage::disk('local')->exists("temp_latex/{$tempDirName}/{$logFileName}")
                             ? Storage::disk('local')->get("temp_latex/{$tempDirName}/{$logFileName}")
                             : 'Log file not found.';
                 Storage::disk('local')->deleteDirectory("temp_latex/{$tempDirName}"); // Cleanup
                 return response()->json(['success' => false, 'message' => "Compilation finished but PDF not found. Log:\n" . Str::limit($logContent, 1000)], 500);
            }

        } else {
            // Compilation failed
            $errorOutput = $process->errorOutput();
            $logContent = Storage::disk('local')->exists("temp_latex/{$tempDirName}/{$logFileName}")
                        ? Storage::disk('local')->get("temp_latex/{$tempDirName}/{$logFileName}")
                        : 'Log file not found.';

            // Clean up the compilation directory
            Storage::disk('local')->deleteDirectory("temp_latex/{$tempDirName}");

            return response()->json([
                'success' => false,
                'message' => "LaTeX compilation failed. Error: " . Str::limit($errorOutput, 500) . "\nLog:\n" . Str::limit($logContent, 1000)
            ], 500);
        }
    }
}
