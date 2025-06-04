@extends('adminlte::page')
@section('title', $item->title)
@section('content')
<div class="card">
    <div class="card-body">
        <h4>{{ $item->title }}</h4>
        <p>{{ $item->description }}</p>
        <x-ringlesoft-approval-actions :model="$item" />
    </div>
</div>
@endsection
