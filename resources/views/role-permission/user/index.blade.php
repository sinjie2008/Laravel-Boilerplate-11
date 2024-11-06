@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>List Users </h1>
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

            <div class="card mt-3">
                <div class="card-header">
                    <h4>
                        @can('create user')
                        <a href="{{ url('admin/users/create') }}" class="btn btn-primary float-end">Add User</a>
                        @endcan
                    </h4>
                </div>
                <div class="card-body">

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if (!empty($user->getRoleNames()))
                                        @foreach ($user->getRoleNames() as $rolename)
                                            <label class="badge bg-primary mx-1">{{ $rolename }}</label>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @can('update user')
                                    <a href="{{ url('admin/users/'.$user->id.'/edit') }}" class="btn btn-success">Edit</a>
                                    @endcan

                                    @can('delete user')
                                    <a href="{{ url('admin/users/'.$user->id.'/delete') }}" class="btn btn-danger mx-2">Delete</a>
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