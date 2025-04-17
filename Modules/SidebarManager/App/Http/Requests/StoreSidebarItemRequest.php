<?php

namespace Modules\SidebarManager\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSidebarItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Implement authorization logic if needed (e.g., check permissions)
        return true; // Allow anyone authenticated for now
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:sidebar_items,id', // Ensure parent exists
            'permission_required' => 'nullable|string|max:255',
            'enabled' => 'boolean', // Handled in controller, but good practice
        ];
    }
}
