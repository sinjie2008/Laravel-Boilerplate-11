{{-- Form fields for creating/editing SidebarItem --}}
@csrf

<div class="form-group">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $item->name ?? '') }}" required>
    @error('name')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="icon">Icon Class</label>
    <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror"
           value="{{ old('icon', $item->icon ?? '') }}" placeholder="e.g., fas fa-fw fa-user">
    <small class="form-text text-muted">Enter Font Awesome icon class (e.g., 'fas fa-tachometer-alt'). Leave blank for default.</small>
    @error('icon')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="route">Route Name or URL</label>
    <input type="text" name="route" id="route" class="form-control @error('route') is-invalid @enderror"
           value="{{ old('route', $item->route ?? '') }}" placeholder="e.g., admin.users.index or /admin/some/path">
    <small class="form-text text-muted">Enter a valid Laravel route name or a relative URL. Leave blank for parent menu items.</small>
    @error('route')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="parent_id">Parent Item</label>
    <select name="parent_id" id="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
        <option value="">-- None (Top Level) --</option>
        {{-- Populate this dropdown dynamically in the controller --}}
        @isset($parentOptions)
            @foreach ($parentOptions as $option)
                {{-- Exclude the item itself if editing --}}
                @if(!isset($item) || $item->id != $option->id)
                <option value="{{ $option->id }}" {{ (old('parent_id', $item->parent_id ?? '') == $option->id) ? 'selected' : '' }}>
                    {{ str_repeat('&nbsp;&nbsp;&nbsp;', $option->depth ?? 0) }}{{ $option->name }}
                </option>
                @endif
            @endforeach
        @endisset
    </select>
    @error('parent_id')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="permission_required">Permission Required</label>
    <input type="text" name="permission_required" id="permission_required" class="form-control @error('permission_required') is-invalid @enderror"
           value="{{ old('permission_required', $item->permission_required ?? '') }}" placeholder="e.g., view users">
    <small class="form-text text-muted">Optional: Enter the permission string required to see this item (e.g., from Spatie Permissions).</small>
    @error('permission_required')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-check">
    <input type="hidden" name="enabled" value="0"> {{-- Hidden field for unchecked case --}}
    <input type="checkbox" name="enabled" id="enabled" class="form-check-input" value="1"
           {{ old('enabled', $item->enabled ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="enabled">Enabled</label>
    <small class="form-text text-muted">Uncheck to hide this item from the sidebar.</small>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-success">{{ isset($item) ? 'Update Item' : 'Create Item' }}</button>
    <a href="{{ route('admin.sidebar.items.index') }}" class="btn btn-secondary">Cancel</a>
</div> 