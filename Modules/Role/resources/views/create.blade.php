@extends('adminlte::page')

@section('title', 'Create Role') {{-- Updated title --}}

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Create Role</h1> {{-- Updated header --}}
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
                    {{-- Updated form action URL --}}
                    <form action="{{ url('/admin/role') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="">Role Name</label>
                            <input type="text" name="name" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                            {{-- Updated Back link URL --}}
                            <a href="{{ url('/admin/role') }}" class="btn btn-danger float-end">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
