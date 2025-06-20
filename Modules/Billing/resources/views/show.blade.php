@extends('adminlte::page')

@section('content')
    <h1>Invoice #{{ $invoice->id }}</h1>
    <ul class="list-group">
        <li class="list-group-item">User: {{ $invoice->user->name }}</li>
        <li class="list-group-item">Amount: {{ $invoice->amount }}</li>
        <li class="list-group-item">Status: {{ $invoice->status }}</li>
        <li class="list-group-item">Due Date: {{ $invoice->due_date->format('Y-m-d') }}</li>
    </ul>
@endsection
