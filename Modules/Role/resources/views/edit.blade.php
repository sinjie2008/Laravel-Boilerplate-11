@extends('adminlte::page')

@section('title', 'Edit Role')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit Role</h1>
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
                    {{-- Updated form action URL for editing --}}
                    <form action="{{ url('/admin/role/'.$role->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                        <div class="mb-3">
                            <label for="">Role Name</label>
                            {{-- Pre-fill the input with the current role name --}}
                            <input type="text" name="name" class="form-control" value="{{ $role->name }}" />
                             @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            {{-- Updated Back link URL --}}
                            <a href="{{ url('/admin/role') }}" class="btn btn-danger float-end">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
