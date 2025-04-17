<?php

namespace Modules\SidebarManager\App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // Assuming standard Laravel Gate
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route as RouteFacade;
use Modules\SidebarManager\App\Models\SidebarItem;

class SidebarService
{
    /**
     * Fetches, filters, and formats sidebar menu items from the database.
     *
     * @return array The formatted menu items array for AdminLTE.
     */
    public function getMenuItems(): array
    {
        // Fetch all enabled items, eager load children recursively and order them
        $items = SidebarItem::whereNull('parent_id')
            ->where('enabled', true)
            ->with('children') // Eager load children
            ->orderBy('order')
            ->get();

        return $this->formatMenuItems($items);
    }

    /**
     * Recursively formats Eloquent models into the AdminLTE menu array structure
     * and filters based on permissions.
     *
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @return array
     */
    protected function formatMenuItems($items): array
    {
        $menu = [];
        $user = Auth::user();

        foreach ($items as $item) {
            // 1. Check Permission
            if ($item->permission_required && (!$user || !$user->can($item->permission_required))) {
                 // Using Spatie permissions: $user->hasPermissionTo($item->permission_required)
                 // Using standard Laravel Gate: Gate::allows($item->permission_required)
                 // Ensure your User model uses HasRoles/HasPermissions trait if using Spatie
                 // Or that the Gate is defined properly.
                continue; // Skip item if permission check fails
            }

            // 2. Format Item
            $formattedItem = [
                'text' => $item->name,
                'icon' => $item->icon ?? 'far fa-circle', // Default icon
                'can'  => $item->permission_required, // Pass permission for AdminLTE check too
            ];

            // Determine if it's a route name or URL
            if ($item->route) {
                if (RouteFacade::has($item->route)) {
                    $formattedItem['route'] = $item->route;
                } else {
                    $formattedItem['url'] = url($item->route); // Assume it's a URL/URI if route doesn't exist
                    Log::warning("SidebarManager: Route '{$item->route}' not found for item '{$item->name}'. Treating as URL.");
                }
            } else {
                 $formattedItem['url'] = '#'; // Default for items without a route/url (like headers or parent menus)
            }


            // 3. Handle Children (Submenu)
            if ($item->children->isNotEmpty()) {
                $formattedChildren = $this->formatMenuItems($item->children);
                // Only add submenu if there are visible children after permission checks
                if (!empty($formattedChildren)) {
                    $formattedItem['submenu'] = $formattedChildren;
                }
            }

            // Add item only if it's a link/route OR it has visible children
            // This prevents adding parent items that have no accessible children
            if (isset($formattedItem['route']) || isset($formattedItem['url']) || isset($formattedItem['submenu'])) {
                $menu[] = $formattedItem;
            }

        }

        return $menu;
    }
} 