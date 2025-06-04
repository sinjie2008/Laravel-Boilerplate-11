@extends('adminlte::page') {{-- Assuming AdminLTE is used and this is the main layout --}}

@section('title', config('fundrequest.name', 'Fund Requests'))

@section('content_header')
    {{-- You can add a dynamic header here if needed --}}
    {{-- <h1>@yield('header_title', 'Fund Requests')</h1> --}}
@stop

@section('content')
    @yield('content')
@stop

@section('css')
    {{-- Add module specific CSS files here --}}
    {{-- <link rel="stylesheet" href="{{ asset('modules/fundrequest/css/app.css') }}"> --}}
@stop

@section('js')
    {{-- Add module specific JS files here --}}
    {{-- <script src="{{ asset('modules/fundrequest/js/app.js') }}"></script> --}}
@stop
