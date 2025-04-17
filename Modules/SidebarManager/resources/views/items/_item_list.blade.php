{{-- Recursive partial to display sidebar items, adjusted for SortableJS --}}

<div class="list-group sortable-group">
    @foreach ($items as $item)
        <div class="list-group-item sidebar-item-entry" data-id="{{ $item->id }}">
            <div class="item-details">
                <div>
                    <span class="handle"><i class="fas fa-arrows-alt"></i></span> {{-- Adjusted handle icon --}}
                    <i class="{{ $item->icon ?? 'far fa-circle' }} fa-fw"></i>
                    <strong>{{ $item->name }}</strong>
                    <small class="text-muted">({{ $item->route ?: ($item->children->count() > 0 ? 'Parent Menu' : '#') }})</small>
                    @if($item->permission_required)
                        <span class="badge badge-secondary">{{ $item->permission_required }}</span>
                    @endif
                    @if(!$item->enabled)
                        <span class="badge badge-warning">Disabled</span>
                    @endif
                </div>
                <div class="item-actions">
                    <a href="{{ route('admin.sidebar.items.edit', $item->id) }}" class="btn btn-xs btn-info">Edit</a>
                    <form action="{{ route('admin.sidebar.items.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This might affect child items.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                    </form>
                </div>
            </div>

            {{-- Recursively include children within a nested group container --}}
            @if ($item->children && $item->children->count() > 0)
                <div class="nested-sortable ml-4"> {{-- Add margin for visual nesting --}}
                    @include('sidebarmanager::items._item_list', ['items' => $item->children])
                </div>
            @endif
        </div>
    @endforeach
</div> 