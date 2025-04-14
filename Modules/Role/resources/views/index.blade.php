@extends('adminlte::page')

@section('title', 'Roles') {{-- Updated title --}}

@section('content_header')
    <h1>Roles</h1> {{-- Updated header --}}
@stop

@section('content')

    {{-- Removed the top navigation links as they might be handled globally --}}
    {{-- <div class="container mt-5">
        <a href="{{ url('admin/roles') }}" class="btn btn-primary mx-1">Roles</a>
        <a href="{{ url('admin/permissions') }}" class="btn btn-info mx-1">Permissions</a>
        <a href="{{ url('admin/users') }}" class="btn btn-warning mx-1">Users</a>
    </div> --}}

    <div class="container mt-2">
        <div class="row">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>
                            Roles
                            @can('create role')
                            {{-- Updated Add Role link --}}
                            <a href="{{ url('/admin/role/create') }}" class="btn btn-primary float-end">Add Role</a>
                            @endcan
                        </h4>
                    </div>
                    <div class="card-body">

                        {{-- Add Search Form --}}
                        <form action="{{ url('/admin/role') }}" method="GET" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search roles..." value="{{ $search ?? '' }}">
                                <button class="btn btn-outline-secondary" type="submit">Search</button>
                            </div>
                        </form>

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
                                        {{-- Updated Add/Edit Permissions link --}}
                                        <a href="{{ url('/admin/role/'.$role->id.'/give-permissions') }}" class="btn btn-warning">
                                            Add / Edit Role Permission
                                        </a>

                                        @can('update role')
                                        {{-- Updated Edit link --}}
                                        <a href="{{ url('/admin/role/'.$role->id.'/edit') }}" class="btn btn-success">
                                            Edit
                                        </a>
                                        @endcan

                                        @can('delete role')
                                        {{-- Updated Delete link (using form for DELETE method) --}}
                                        <form action="{{ url('/admin/role/'.$role->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mx-2" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Add Pagination Links --}}
                        <div class="mt-3">
                            {{ $roles->appends(['search' => $search ?? ''])->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

@stop
