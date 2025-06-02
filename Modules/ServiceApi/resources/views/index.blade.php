@extends('serviceapi::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('serviceapi.name') !!}</p>
@endsection
