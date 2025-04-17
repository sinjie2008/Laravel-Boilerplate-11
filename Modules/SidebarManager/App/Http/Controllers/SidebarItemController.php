<?php

namespace Modules\SidebarManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\SidebarManager\App\Http\Requests\StoreSidebarItemRequest;
use Modules\SidebarManager\App\Http\Requests\UpdateSidebarItemRequest;
use Modules\SidebarManager\App\Models\SidebarItem;

class SidebarItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $items = SidebarItem::whereNull('parent_id')
                            ->with('children')
                            ->orderBy('order')
                            ->get();

        return view('sidebarmanager::items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parentOptions = $this->getFlattenedSidebarItems();
        return view('sidebarmanager::items.create', compact('parentOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSidebarItemRequest $request): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            // Handle checkbox (set enabled to 0 if not present in request, validated() handles it if present)
            $validatedData['enabled'] = $request->has('enabled');

            // Set order to the max order for the current parent + 1
            $maxOrder = SidebarItem::where('parent_id', $validatedData['parent_id'] ?? null)->max('order');
            $validatedData['order'] = $maxOrder !== null ? $maxOrder + 1 : 0;

            SidebarItem::create($validatedData);

            return redirect()->route('admin.sidebar.items.index')
                             ->with('success', 'Sidebar item created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating sidebar item: '. $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Failed to create sidebar item.')
                             ->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('sidebarmanager::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SidebarItem $item): View
    {
        $parentOptions = $this->getFlattenedSidebarItems($item->id);
        return view('sidebarmanager::items.edit', compact('item', 'parentOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSidebarItemRequest $request, SidebarItem $item): RedirectResponse
    {
         try {
            $validatedData = $request->validated();
            $validatedData['enabled'] = $request->has('enabled');

            // Prevent setting parent_id to self or descendant (handled in UpdateSidebarItemRequest)

            // Check if parent_id changed, if so, update order
            if ($item->parent_id != ($validatedData['parent_id'] ?? null)) {
                $maxOrder = SidebarItem::where('parent_id', $validatedData['parent_id'] ?? null)->max('order');
                $validatedData['order'] = $maxOrder !== null ? $maxOrder + 1 : 0;
                 // Optionally re-order siblings of the old parent?
            }

            $item->update($validatedData);

            return redirect()->route('admin.sidebar.items.index')
                             ->with('success', 'Sidebar item updated successfully.');
         } catch (\Exception $e) {
            Log::error('Error updating sidebar item: '. $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Failed to update sidebar item.')
                             ->withInput();
         }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SidebarItem $item): RedirectResponse
    {
        try {
            // Basic check: Prevent deletion if it has children
            if ($item->children()->count() > 0) {
                 return redirect()->route('admin.sidebar.items.index')
                                 ->with('error', 'Cannot delete item: It has child items.');
            }

            $item->delete();
            // Optionally re-order siblings after deletion?

            return redirect()->route('admin.sidebar.items.index')
                             ->with('success', 'Sidebar item deleted successfully.');
        } catch (\Exception $e) {
             Log::error('Error deleting sidebar item: '. $e->getMessage());
             return redirect()->route('admin.sidebar.items.index')
                              ->with('error', 'Failed to delete sidebar item.');
        }
    }

    /**
     * Update the order of sidebar items.
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $data = $request->input('orderData');

        if (!$data) {
            return response()->json(['status' => 'error', 'message' => 'No order data received.'], 400);
        }

        try {
            DB::beginTransaction();

            $this->updateRecursiveOrder($data);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Sidebar order updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sidebar order update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update sidebar order. Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Recursive function to update item order and parentage.
     *
     * @param array $items The array structure from SortableJS
     * @param int|null $parentId The parent ID for the current level
     */
    protected function updateRecursiveOrder(array $items, $parentId = null): void
    {
        foreach ($items as $index => $itemData) {
            if (!isset($itemData['id'])) {
                Log::warning('Sidebar order update: Skipping item with missing ID.', ['itemData' => $itemData]);
                continue;
            }

            $item = SidebarItem::find($itemData['id']);
            if ($item) {
                $item->order = $index + 1; // Re-calculate order based on position
                $item->parent_id = $parentId;
                $item->save();

                // If item has children, recurse
                if (isset($itemData['children']) && is_array($itemData['children'])) {
                    $this->updateRecursiveOrder($itemData['children'], $item->id);
                }
            } else {
                Log::warning('Sidebar order update: Item not found.', ['id' => $itemData['id']]);
            }
        }
    }

    /**
     * Helper function to get a flattened list of sidebar items for dropdowns.
     *
     * @param int|null $excludeId ID of the item to exclude (and its children) for edit forms.
     * @return array
     */
    private function getFlattenedSidebarItems($excludeId = null): array
    {
        $options = [];
        $items = SidebarItem::orderBy('order')->get();

        $excludeIds = [];
        if ($excludeId !== null) {
            $itemToExclude = $items->find($excludeId);
            if ($itemToExclude) {
                 $excludeIds = $this->getDescendantIds($itemToExclude, $items);
                 $excludeIds[] = $excludeId;
            }
        }

        $items = $items->whereNotIn('id', $excludeIds);
        $nestedItems = $this->buildNestedArray($items);

        $this->flattenNestedArray($nestedItems, $options);

        return $options;
    }

     /**
     * Recursive helper to get all descendant IDs.
     */
    private function getDescendantIds(SidebarItem $item, $allItems): array
    {
        $ids = [];
        $children = $allItems->where('parent_id', $item->id);
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child, $allItems));
        }
        return $ids;
    }

    /**
     * Helper to build a nested array structure from flat collection.
     */
    private function buildNestedArray($items, $parentId = null): array
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildNestedArray($items, $item->id);
                if ($children) {
                    $item->children = $children;
                }
                $branch[] = $item;
            }
        }
        return $branch;
    }

    /**
     * Recursive helper to flatten the nested array for select options.
     */
    private function flattenNestedArray($items, &$result, $depth = 0)
    {
        foreach ($items as $item) {
            $item->depth = $depth; // Add depth for indentation
            $result[] = $item;
            if (isset($item->children)) {
                $this->flattenNestedArray($item->children, $result, $depth + 1);
            }
        }
    }
}
