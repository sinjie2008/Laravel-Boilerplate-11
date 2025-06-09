@extends('adminlte::page')

@section('title', 'Post Details')

@section('content_header')
    <h1>{{ $post->title }}</h1>
@endsection

@section('content')
    <p>{{ $post->content }}</p>
@endsection
