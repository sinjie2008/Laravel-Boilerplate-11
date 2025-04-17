@extends('adminlte::page')

@section('title', 'Create Sidebar Item')

@section('content_header')
    <h1>Create New Sidebar Item</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sidebar.items.store') }}" method="POST">
                @include('sidebarmanager::items._form')
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- Add CSS if needed --}}
@stop

@section('js')
    {{-- Add JS if needed --}}
@stop 