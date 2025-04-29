<?php

namespace Modules\LatexManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage; // Added for potential future use

class LatexManagerController extends Controller
{
    /**
     * Display the LaTeX editor view.
     */
    public function index(Request $request)
    {
        // Retrieve latex content from session if available (after compilation)
        $latexContent = $request->session()->get('latexContent', '');
        return view('latexmanager::index', compact('latexContent'));
    }

    /**
     * Compile the submitted LaTeX code.
     */
    public function compile(Request $request): RedirectResponse
    {
        $latexContent = $request->input('latex', '');
        $tempDir = storage_path('app/latex'); // Use storage path for better security/organization
        $texFile = $tempDir . '/document.tex';
        $pdfFile = $tempDir . '/document.pdf';

        // Ensure the directory exists
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0777, true, true);
        }

        // Save the LaTeX content to the .tex file
        File::put($texFile, $latexContent);

        // Define the path to pdflatex - Consider making this configurable
        // IMPORTANT: Ensure this path is correct for the server environment
        $pdflatexPath = '"C:\Users\sIn.jie\AppData\Local\Programs\MiKTeX\miktex\bin\x64\pdflatex"'; 
        
        // Escape arguments for security
        $escapedTempDir = escapeshellarg($tempDir);
        $escapedTexFile = escapeshellarg($texFile);

        // Compile the LaTeX file to PDF
        // Added -interaction=nonstopmode to prevent pdflatex from stopping on errors
        $command = "{$pdflatexPath} -interaction=nonstopmode -output-directory={$escapedTempDir} {$escapedTexFile} 2>&1";
        
        // Execute the command
        $output = shell_exec($command);
        
        // Check if PDF was generated successfully (basic check)
        $pdfGenerated = File::exists($pdfFile);

        // Store content in session to repopulate editor
        $request->session()->flash('latexContent', $latexContent);

        // Redirect back to the editor page
        // Optionally add success/error messages based on $output or $pdfGenerated
        return redirect()->route('latex.editor')
                         ->with('compile_output', $output) // Optional: pass output for debugging
                         ->with('pdf_generated', $pdfGenerated); 
    }

    /**
     * Serve the generated PDF file.
     */
    public function servePdf()
    {
        $pdfFile = storage_path('app/latex/document.pdf');

        if (File::exists($pdfFile)) {
            // Return the PDF file with appropriate headers
            return response()->file($pdfFile, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="document.pdf"' // Display inline in browser
            ]);
        } else {
            // Return a 404 response if the PDF doesn't exist
            abort(404, 'PDF not found. Please compile the LaTeX document first.');
        }
    }
}
