@extends('adminlte::page')

@section('title', 'Create Permission')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Create User</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        
        </div>
    </div>   
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">

            @if ($errors->any())
            <ul class="alert alert-warning">
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ url('admin/users') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="">Name</label>
                            <input type="text" name="name" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label for="">Email</label>
                            <input type="text" name="email" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label for="">Password</label>
                            <input type="text" name="password" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label for="">Roles</label>
                            <select name="roles[]" class="form-control" multiple>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ url('admin/users') }}" class="btn btn-danger float-end">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop