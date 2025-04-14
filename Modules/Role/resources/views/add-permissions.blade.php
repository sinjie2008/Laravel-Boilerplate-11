@extends('adminlte::page')

@section('title', 'Add Permissions to Role') {{-- Updated title slightly --}}

@section('content_header')
    <h1>Add Permissions to Role</h1> {{-- Updated header slightly --}}
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Role : {{ $role->name }}</h4>
                </div>
                <div class="card-body">

                    {{-- Updated form action URL --}}
                    <form action="{{ url('/admin/role/'.$role->id.'/give-permissions') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            @error('permission')

                            <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <label for="">Permissions</label>

                            <div class="row">
                                @foreach ($permissions as $permission)
                                <div class="col-md-2">
                                    <label>
                                        <input
                                            type="checkbox"
                                            name="permission[]"
                                            value="{{ $permission->name }}"
                                            {{ in_array($permission->id, $rolePermissions) ? 'checked':'' }}
                                        />
                                        {{ $permission->name }}
                                    </label>
                                </div>
                                @endforeach

                            </div>

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
