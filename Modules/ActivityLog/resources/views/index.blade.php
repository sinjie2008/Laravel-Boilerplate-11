@extends('adminlte::page')

@section('title', 'Activity Logs') {{-- Updated title --}}

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>List Activity Logs</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('activitylog.index') }}" method="GET" class="float-end"> {{-- Use module route name --}}
                                <div class="input-group">
                                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search...">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th class="d-none">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at }}</td>
                                    <td>{{ $activity->causer_name ? $activity->causer_name : 'System' }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->subject_type }}</td>
                                    <td class="d-none">
                                        @if($activity->properties)
                                            {{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Removed default pagination link, footer handles it --}}
                </div>

                <div class="card-footer">
                    {{ $activities->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                </div>
            </div>

        </div>
    </div>

@stop

@section('css')
    {{-- Removed link to /css/admin_custom.css as it should be handled globally --}}
@stop

@section('js')
    <script>
        // Removed DataTable initialization as it might conflict or be handled globally
        // $(document).ready(function() {
        //     $('.table').DataTable(); 
        // });
    </script>
@stop
