@extends('adminlte::page')

@section('title', 'Edit Sidebar Item')

@section('content_header')
    <h1>Edit Sidebar Item: {{ $item->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sidebar.items.update', $item->id) }}" method="POST">
                @method('PUT')
                @include('sidebarmanager::items._form', ['item' => $item]) {{-- Pass the item to the form --}}
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