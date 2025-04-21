@extends('adminlte::page')

@section('title', 'Activity Log')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Activity Log</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Activity Log</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activities</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="activities-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>User</th>
                                <th>Subject</th>
                                <th>Timestamp</th>
                                <th>Properties</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ optional($activity->causer)->name ?? 'System' }}</td>
                                    <td>
                                        @if($activity->subject)
                                            {{ class_basename($activity->subject_type) }} ID: {{ $activity->subject_id }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $activity->created_at->diffForHumans() }} ({{ $activity->created_at }})</td>
                                    <td>
                                        @if($activity->properties->count())
                                            <pre>{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No activities recorded yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        {{ $activities->links() }} {{-- Display pagination links --}}
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@push('styles')
    {{-- Add any specific styles for this page if needed --}}
@endpush

@push('scripts')
    {{-- Add any specific scripts for this page if needed --}}
    <script>
        // You could add DataTables initialization here for sorting/filtering
        // $(function () {
        //     $("#activities-table").DataTable();
        // });
    </script>
@endpush
