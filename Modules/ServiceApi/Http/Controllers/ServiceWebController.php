<?php

namespace Modules\ServiceApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ServiceApi\app\Models\Service;

class ServiceWebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Service::query();
            $total = $query->count();
            $filtered = $total; // For now, no filtering logic
            $services = $query->get();

            $data = [];
            foreach ($services as $service) {
                $data[] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'action' => view('serviceapi::services.partials.actions', compact('service'))->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $data,
            ]);
        }
        return view('serviceapi::services.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('serviceapi::services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Save to database with authenticated user ID
        Service::create($request->only('name', 'description') + ['user_id' => auth()->id()]);
        return redirect()->route('serviceapi.services.index')->with('success', 'Service created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch service from database
        $service = Service::findOrFail($id);
        return view('serviceapi::services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Fetch service from database
        $service = Service::findOrFail($id);
        return view('serviceapi::services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Update in database
        $service = Service::findOrFail($id);
        $service->update($request->only('name', 'description'));
        return redirect()->route('serviceapi.services.index')->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete from database
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect()->route('serviceapi.services.index')->with('success', 'Service deleted successfully!');
    }
}
