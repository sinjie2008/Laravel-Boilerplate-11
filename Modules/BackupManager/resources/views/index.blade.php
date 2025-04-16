@extends('adminlte::page') {{-- Use the main AdminLTE layout --}}

@section('title', 'Backup Manager') {{-- Set the page title --}}

@section('content_header')
    <h1>Backup Manager</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                 @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Existing Backups</h3>
                        <div class="card-tools">
                            <form id="createBackupForm" action="{{ route('backup-manager.create') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" id="createBackupBtn" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Create New Backup
                                </button>
                                <span id="backupLoadingSpinner" style="display: none; margin-left: 10px;">
                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                </span>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            <small>Backups are stored in: <code>{{ storage_path('app/' . config('backup.backup.name')) }}</code></small>
                        </p>
                        <table id="backupsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Last Modified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($backups as $key => $backup)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $backup['file_name'] }}</td>
                                        <td>{{ $backup['file_size'] }}</td>
                                        <td>{{ $backup['last_modified'] }}</td>
                                        <td>
                                            <a href="{{ route('backup-manager.download', $backup['file_name']) }}" class="btn btn-success btn-xs" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            {{-- Restore DB Button --}}
                                            <form action="{{ route('backup-manager.restore-database', $backup['file_name']) }}" method="POST" style="display: inline;" onsubmit="return confirm('WARNING: This will OVERWRITE your current database with the data from this backup ({{ $backup['file_name'] }}). This cannot be undone. Are you absolutely sure?');">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-xs" title="Restore Database Only">
                                                    <i class="fas fa-database"></i> <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            {{-- Delete Button --}}
                                            <form action="{{ route('backup-manager.destroy', $backup['file_name']) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No backups found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(function () {
        // Optional: Initialize DataTables if you uncommented the section above
        // $("#backupsTable").DataTable({...});

        // Handle backup creation button click
        $('#createBackupForm').on('submit', function() {
            // Disable button and show spinner
            $('#createBackupBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Starting Backup...');
            // $('#backupLoadingSpinner').show(); // Alternative: show separate spinner

            // Allow form submission to proceed
            return true;
        });

        // Re-enable button if the user navigates back without page reload (less common)
        $(window).on('pageshow', function() {
             if (!$('#createBackupBtn').prop('disabled')) return; // Only if it was disabled

             // Check if a success/error message exists from the server potentially indicating completion/failure
             if ($('.alert-success').length > 0 || $('.alert-danger').length > 0 || $('.alert-info').length > 0) {
                 // If a message exists, assume process finished or user navigated away/back
                 $('#createBackupBtn').prop('disabled', false).html('<i class="fas fa-plus"></i> Create New Backup');
                 // $('#backupLoadingSpinner').hide();
             }
             // If no message, it might still be processing, keep it disabled.
             // A more robust solution would involve polling or websockets for true status.
        });
    });
</script>
@stop
{{-- Optional: Add scripts for DataTables if you use them --}}
{{-- @section('js')
<script>
    $(function () {
        $("#backupsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"] // Example buttons
        }).buttons().container().appendTo('#backupsTable_wrapper .col-md-6:eq(0)');
    });
</script>
@stop --}}
