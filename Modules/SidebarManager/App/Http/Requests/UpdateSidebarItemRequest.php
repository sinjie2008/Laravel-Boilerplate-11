<?php

namespace Modules\SidebarManager\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\SidebarManager\App\Models\SidebarItem;

class UpdateSidebarItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow anyone authenticated for now
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $itemId = $this->route('item')->id; // Get the ID of the item being edited

        // Get IDs of all descendants of the current item
        $descendantIds = $this->getDescendantIds(SidebarItem::find($itemId));

        return [
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('sidebar_items', 'id'),
                // Prevent setting parent to self or a descendant
                Rule::notIn(array_merge([$itemId], $descendantIds))
            ],
            'permission_required' => 'nullable|string|max:255',
            'enabled' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'parent_id.not_in' => 'Cannot set the parent to itself or one of its descendants.',
        ];
    }

    /**
     * Recursive helper to get all descendant IDs.
     * Note: This duplicates the logic from the controller. Consider moving to a trait or the model.
     */
    private function getDescendantIds(?SidebarItem $item): array
    {
        if (!$item) {
            return [];
        }
        $ids = [];
        // Eager load children to avoid multiple queries in recursion
        $item->load('children');
        foreach ($item->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }
        return $ids;
    }
}
