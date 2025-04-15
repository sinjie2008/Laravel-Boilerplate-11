<?php

namespace Modules\Document\App\Http\Controllers;

use App\Http\Controllers\Controller; // Keep using the base Controller if needed, or adjust if the module has its own base
use Modules\Document\App\Models\Document; // Adjust namespace for the model
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse; // Keep this if needed, or remove if not used

class DocumentController extends Controller
{

    public function __construct()
    {
        // Permissions might need adjustment depending on how they are managed globally vs per module
        $this->middleware('permission:view documents', ['only' => ['index']]);
        $this->middleware('permission:create documents', ['only' => ['create','store']]);
        $this->middleware('permission:update documents', ['only' => ['update','edit']]);
        $this->middleware('permission:delete documents', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ensure the model namespace is correct
        $documents = Document::latest()->get();
        // Adjust view path to use the module's view namespace
        return view('document::index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Adjust view path
        return view('document::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse // Keep RedirectResponse type hint if applicable
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        // Ensure the model namespace is correct
        $document = Document::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->hasFile('document')) {
            // Ensure Spatie Media Library is configured correctly for modules if needed
            $document->addMediaFromRequest('document')
                ->toMediaCollection('documents');
        }

        // Adjust route name if necessary (module routes might be prefixed)
        return redirect()->route('document.documents.index') // Use the module's named route
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document) // Use route model binding with the correct model namespace
    {
        // Adjust view path
        return view('document::show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document) // Use route model binding with the correct model namespace
    {
        // Adjust view path
        return view('document::edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document): RedirectResponse // Use route model binding & type hint
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        $document->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->hasFile('document')) {
            $document->clearMediaCollection('documents');
            $document->addMediaFromRequest('document')
                ->toMediaCollection('documents');
        }

        // Adjust route name if necessary
        return redirect()->route('document.documents.index') // Use the module's named route
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document) // Use route model binding
    {
        $document->delete();
        // Adjust route name if necessary
        return redirect()->route('document.documents.index') // Use the module's named route
            ->with('success', 'Document deleted successfully.');
    }
}
