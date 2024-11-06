@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>List Role</h1>
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
                            @can('create permission')
                            <a href="{{ url('admin/roles/create') }}" class="btn btn-primary float-end">Add Role</a>
                            @endcan
                        </div>
                        <div class="col-md-6">
                            <form action="" method="GET" class="float-end">
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
                                <th>Id</th>
                                <th>Name</th>
                                <th width="40%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <a href="{{ url('admin/roles/'.$role->id.'/give-permissions') }}" class="btn btn-warning">
                                        Add / Edit Role Permission
                                    </a>

                                    @can('delete role')
                                    <a href="{{ url('admin/roles/'.$role->id.'/delete') }}" class="btn btn-danger mx-2">
                                        Delete
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="card-footer">
                    {{ $roles->appends(['search' => $search])->links('pagination::bootstrap-4') }}
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