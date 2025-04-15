@extends('adminlte::page')

@section('title', 'Module Manager')

@section('plugins.Datatables', true) {{-- Enable DataTables for this page --}}

@section('content_header')
    <h1>Module Manager</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"></h3>
        </div>
        <div class="card-body">
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

            <table id="modulesTable" class="table table-bordered table-striped"> {{-- Add ID --}}
                <thead>
                    <tr>
                        <th>Module Name</th>
                        <th>Status</th>
                        <th>Path</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
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
                                @if ($module->isEnabled())
                                    {{-- Deactivate Button --}}
                                    @if (strtolower($module->getName()) !== 'modulemanager') {{-- Prevent deactivating self --}}
                                        <form action="{{ route('module-manager.deactivate', $module->getName()) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">Deactivate</button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-warning btn-sm" disabled>Deactivate</button>
                                    @endif
                                @else
                                    {{-- Activate Button --}}
                                    <form action="{{ route('module-manager.activate', $module->getName()) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Activate</button>
                                    </form>
                                @endif

                                {{-- Uninstall Button --}}
                                @if (strtolower($module->getName()) !== 'modulemanager') {{-- Prevent uninstalling self --}}
                                    <form action="{{ route('module-manager.uninstall', $module->getName()) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('WARNING: This will DELETE the module files ({{ $module->getName() }}). This action cannot be undone. Are you absolutely sure?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Uninstall</button>
                                    </form>
                                @else
                                     <button type="button" class="btn btn-danger btn-sm" disabled>Uninstall</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No modules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    {{-- Add custom CSS if needed --}}
@stop

@section('js')
    <script>
        $(document).ready(function() {
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
        });
    </script>
@stop
