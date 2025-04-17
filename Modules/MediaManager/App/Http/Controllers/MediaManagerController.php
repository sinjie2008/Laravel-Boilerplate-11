<?php

namespace Modules\MediaManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Support\Renderable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(): Renderable
    {
        // Fetch all media items for client-side DataTables
        // Consider server-side processing for large datasets
        $mediaItems = Media::latest()->get(); // Fetch all items

        // Return the view from the module, passing the media items
        return view('mediamanager::index', compact('mediaItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mediamanager::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mediaFile' => 'required|array', // Ensure it's an array because of `multiple` input
            'mediaFile.*' => 'required|file|max:10240' // Validate each file (max 10MB)
            // Add other validation rules like mimes if needed: 'mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

        try {
            // Use a temporary model instance to add media unattached to any specific record
            $tempModel = new class extends \Illuminate\Database\Eloquent\Model { 
                use \Spatie\MediaLibrary\InteractsWithMedia;
            };

            // Loop through uploaded files
            if ($request->hasFile('mediaFile')) {
                foreach ($request->file('mediaFile') as $file) {
                    $tempModel->addMedia($file)
                        ->toMediaCollection(); // Add to default collection
                        // ->toMediaCollection($request->input('collection_name', 'default')); // Optional: use a specific collection
                }
            }

            return redirect()->route('mediamanager.index')
                             ->with('success', __('Media uploaded successfully.'));

        } catch (\Exception $e) {
            Log::error('Media upload failed: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', __('Media upload failed. Please try again.'))
                             ->withInput();
        }
    }

    /**
     * Show the specified resource (used for download).
     * @param Media $media
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Media $media): Response | StreamedResponse | BinaryFileResponse
    {
        // Use the built-in download response generator from medialibrary
        // return $media; // Returning the model directly caused a TypeError with the strict type hint
        
        // Explicitly create and return a download response
        return response()->download($media->getPath(), $media->file_name);
    }

    /**
     * Show the form for editing the specified resource.
     * @param Media $media
     * @return Renderable
     */
    public function edit(Media $media): Renderable
    {
        // We'll create this view later if needed
        return view('mediamanager::edit', compact('media'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Media $media
     * @return RedirectResponse
     */
    public function update(Request $request, Media $media): RedirectResponse
    {
        // Add update logic here later (e.g., renaming, custom properties)
        $request->validate([
            'name' => 'required|string|max:255',
            // Add validation for other fields like custom properties
        ]);

        $media->name = $request->input('name');
        // Update custom properties if needed
        // $media->setCustomProperty('alt_text', $request->input('alt_text'));
        $media->save();

        return redirect()->route('mediamanager.index')->with('success', __('Media updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     * @param Media $media
     * @return RedirectResponse
     */
    public function destroy(Media $media): RedirectResponse
    {
        try {
            $media->delete();
            return redirect()->route('mediamanager.index')
                             ->with('success', __('Media item deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Media deletion failed: ' . $e->getMessage());
            return redirect()->route('mediamanager.index')
                             ->with('error', __('Failed to delete media item.'));
        }
    }
}
