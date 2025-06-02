<?php

namespace Modules\ServiceApi\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\ServiceApi\Models\Service;
use Modules\ServiceApi\Transformers\ServiceResource; // Corrected namespace
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    public function __construct()
    {
        // Apply middleware to all methods in this controller
        // The actual middleware strings are defined in app/Http/Kernel.php
        // Permissions like 'can:create_service' will be checked within each method
        // $this->middleware(['auth:sanctum', 'subscription.active', 'subscription.ratelimit']);
        // Specific permission checks will be done per method using $request->user()->can(...)
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Authorization: Check if user can view any services
        // This is a basic check; you might have more granular permissions
        if ($request->user()->cannot('viewAny', Service::class) && !$request->user()->can('view_any_services')) {
             // Using a generic permission name as an example
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to view services.');
        }

        // Typically, users should only see their own services unless they are admins
        // For simplicity, this example fetches services for the authenticated user.
        // Add admin logic if admins can see all services.
        $services = Service::where('user_id', $request->user()->id)
                            ->latest()
                            ->paginate(15);

        return ServiceResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): ServiceResource
    {
        if ($request->user()->cannot('create', Service::class) && !$request->user()->can('create_service')) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to create services.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Add other validation rules for your service fields
        ]);

        $service = Service::create([
            'user_id' => $request->user()->id,
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        return new ServiceResource($service);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Service $service): ServiceResource
    {
        if ($request->user()->cannot('view', $service) && !$request->user()->can('view_service')) {
             // Assuming a generic 'view_service' permission or policy
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to view this service.');
        }
        // Ensure the user owns the service or has global view rights
        if ($service->user_id !== $request->user()->id && $request->user()->cannot('view_any_services')) {
            abort(Response::HTTP_FORBIDDEN, 'You do not own this service.');
        }


        return new ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service): ServiceResource
    {
        if ($request->user()->cannot('update', $service) && !$request->user()->can('update_service')) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to update this service.');
        }
        // Ensure the user owns the service
        if ($service->user_id !== $request->user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'You do not own this service.');
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $service->update($validatedData);

        return new ServiceResource($service->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Service $service): JsonResponse
    {
        if ($request->user()->cannot('delete', $service) && !$request->user()->can('delete_service')) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to delete this service.');
        }
        // Ensure the user owns the service
        if ($service->user_id !== $request->user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'You do not own this service.');
        }

        $service->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
