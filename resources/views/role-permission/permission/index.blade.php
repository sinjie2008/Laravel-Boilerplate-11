@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>List Permissions</h1>
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
                    @can('create permission')
                    <a href="{{ url('admin/permissions/create') }}" class="btn btn-primary float-end">Add Permission</a>
                    @endcan
                </div>
                <div class="card-body">

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    @can('update permission')
                                    <a href="{{ url('admin/permissions/'.$permission->id.'/edit') }}" class="btn btn-success">Edit</a>
                                    @endcan

                                    @can('delete permission')
                                    <a href="{{ url('admin/permissions/'.$permission->id.'/delete') }}" class="btn btn-danger mx-2">Delete</a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    @stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
@stop