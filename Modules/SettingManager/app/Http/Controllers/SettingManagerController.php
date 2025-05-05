<?php

namespace Modules\SettingManager\App\Http\Controllers; // Corrected namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\View\View;
use Modules\SettingManager\App\Models\Setting; // Import the Setting model

class SettingManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $settings = Setting::all()->pluck('value', 'key'); // Fetch settings as key => value pairs
        return view('settingmanager::index', compact('settings')); // Pass settings to the view
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $dataToUpdate = $request->except('_token', '_method', 'site_logo'); // Exclude logo initially

        // Handle file upload for site_logo
        if ($request->hasFile('site_logo')) {
            // Optional: Add validation for the logo file
            $request->validate([
                'site_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Example validation
            ]);

            // Delete old logo if it exists
            $oldLogoPath = Setting::where('key', 'site_logo')->value('value');
            if ($oldLogoPath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldLogoPath))) {
                 Storage::disk('public')->delete(str_replace('/storage/', '', $oldLogoPath));
            }

            // Store the new logo
            $path = $request->file('site_logo')->store('logos', 'public'); // Store in storage/app/public/logos
            $dataToUpdate['site_logo'] = Storage::url($path); // Store the public URL (/storage/logos/...)
        }

        // Update settings in the database
        foreach ($dataToUpdate as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? ''] // Use empty string if value is null
            );
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!'); // Redirect back to index
    }

    // Removed unused methods: create, store, show, edit, destroy
}
