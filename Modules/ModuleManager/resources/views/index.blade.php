@extends('adminlte::page')

@section('title', 'Module Manager')

@section('plugins.Datatables', true) {{-- Enable DataTables for this page --}}

@section('content_header')
    <h1>Module Manager</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Installed Modules</h3>
        </div>
        <div class="card-body">
            {{-- Upload Module Form --}}
            <div class="card card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title">Upload New Module</h3>
                </div>
                <form action="{{ route('module-manager.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="module_zip">Module ZIP File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="module_zip" name="module_zip" accept=".zip" required>
                                    <label class="custom-file-label" for="module_zip">Choose file</label>
                                </div>
                            </div>
                            @error('module_zip')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Upload & Install</button>
                    </div>
                </form>
            </div>
            {{-- End Upload Module Form --}}

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ session('error') }}
                </div>
            @endif
            {{-- End Flash Messages --}}

            {{-- Explicit Check for Modules --}}
            {{-- Check if $modules is set and not empty before trying to access it --}}
            @isset($modules)
                @if(empty($modules)) {{-- Use empty() for arrays --}}
                    <div class="alert alert-warning">
                        No modules were found by the system. Ensure modules are correctly registered and caches are cleared.
                    </div>
                @else
                    <p>Found {{ count($modules) }} module(s).</p> {{-- Use count() for arrays --}}
                @endif
            @else
                <div class="alert alert-danger">
                    Error: The \$modules variable is not available in the view. Controller might have an issue.
                </div>
            @endisset
            {{-- End Explicit Check --}}


            {{-- Modules Table --}}
            {{-- Hide table initially if empty or not set, DataTables will handle showing it if populated --}}
            <table id="modulesTable" class="table table-bordered table-striped" @if(!isset($modules) || empty($modules)) style="display: none;" @endif> {{-- Add ID and conditional style, use empty() --}}
                <thead>
                    <tr>
                        <th>Module Name</th>
                        <th>Status</th>
                        <th>Path</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Ensure $modules exists before looping --}}
                    @isset($modules)
                        @forelse ($modules as $module)
                            <tr>
                                <td>{{ $module->getName() }}</td>
                                <td>
                                    @if ($module->isEnabled())
                                        <span class="badge badge-success">Enabled</span>
                                    @else
                                        <span class="badge badge-secondary">Disabled</span>
                                    @endif
                                </td>
                                <td>{{ $module->getPath() }}</td>
                                <td>
                                    {{-- View Button Logic --}}
                                    @php
                                        $moduleName = $module->getName();
                                        $lowerModuleName = strtolower($moduleName);
                                        $kebabModuleName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $moduleName));

                                        // Define potential route name patterns
                                        $patterns = [
                                            // 1. Admin prefix + Kebab-case: admin.module-name.index (e.g., admin.sql-generator.index)
                                            'admin.' . $kebabModuleName . '.index',
                                            // 2. Admin prefix + Lowercase: admin.module.index (e.g., admin.role.index)
                                            'admin.' . $lowerModuleName . '.index',
                                            // 3. Resource route: module.pluralModule.index (e.g., document.documents.index)
                                            $lowerModuleName . '.' . Illuminate\Support\Str::plural($lowerModuleName) . '.index',
                                            // 4. Simple lowercase: module.index (e.g., activitylog.index)
                                            $lowerModuleName . '.index',
                                            // 5. Kebab-case: module-name.index (e.g., backup-manager.index)
                                            $kebabModuleName . '.index',
                                        ];

                                        $viewRouteName = null;
                                        // Specific check for SettingManager
                                        if ($moduleName === 'SettingManager') {
                                            if (Route::has('settings.index')) {
                                                $viewRouteName = 'settings.index';
                                            }
                                        } else {
                                            // Original pattern matching for other modules
                                            foreach ($patterns as $pattern) {
                                                if (Route::has($pattern)) {
                                                    $viewRouteName = $pattern;
                                                    break; // Use the first match found
                                                }
                                            }
                                        }
                                    @endphp
                                    {{-- Always display the View button and ensure it's clickable --}}
                                    <a href="{{ $viewRouteName ? route($viewRouteName) : '#' }}"
                                       class="btn btn-info btn-sm"
                                       style="display: inline-block;">
                                        View
                                    </a>

                                    {{-- Activate/Deactivate Button --}}
                                    @if ($module->isEnabled())
                                        {{-- Deactivate Button --}}
                                        @if (strtolower($moduleName) !== 'modulemanager') {{-- Prevent deactivating self --}}
                                            <form action="{{ route('module-manager.deactivate', $moduleName) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">Deactivate</button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-warning btn-sm" disabled>Deactivate</button>
                                        @endif
                                    @else
                                        {{-- Activate Button --}}
                                        <form action="{{ route('module-manager.activate', $moduleName) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Activate</button>
                                        </form>
                                    @endif

                                    {{-- Uninstall Button --}}
                                    @if (strtolower($moduleName) !== 'modulemanager') {{-- Prevent uninstalling self --}}
                                        <form action="{{ route('module-manager.uninstall', $moduleName) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('WARNING: This will DELETE the module files ({{ $moduleName }}). This action cannot be undone. Are you absolutely sure?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Uninstall</button>
                                        </form>
                                    @else
                                         <button type="button" class="btn btn-danger btn-sm" disabled>Uninstall</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            {{-- This part might not be reached if the table is hidden when empty, but keep for robustness --}}
                            <tr>
                                <td colspan="4" class="text-center">No modules found.</td>
                            </tr>
                        @endforelse
                    @endisset
                </tbody>
            </table>
        </div> {{-- End card-body --}}
    </div> {{-- End card --}}
@stop

@section('css')
    {{-- Add custom CSS if needed --}}
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Only initialize DataTable if the table element exists and is visible (i.e., modules were found)
            if ($('#modulesTable').is(':visible')) {
                $('#modulesTable').DataTable({
                    "paging": true,       // Enable pagination
                    "searching": true,    // Enable searching
                    "ordering": true,     // Enable sorting
                    "info": true,         // Show table information
                    "autoWidth": false,   // Disable auto width calculation
                    "responsive": true,   // Enable responsiveness
                    // Add specific column definitions if needed, e.g., disable sorting on 'Actions'
                    "columnDefs": [
                        { "orderable": false, "targets": 3 } // Disable sorting for the 4th column (Actions)
                    ]
                });
            }

            // Script to show filename in custom file input
            $('.custom-file-input').on('change', function() {
               let fileName = $(this).val().split('\\').pop();
               $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@stop
