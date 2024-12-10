<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{

    public function __construct()
    {
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
        $documents = Document::latest()->get();
        return view('documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        $document = Document::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->hasFile('document')) {
            $document->addMediaFromRequest('document')
                ->toMediaCollection('documents');
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
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

        return redirect()->route('documents.index')
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }
}
