@extends('adminlte::page')

@section('title', 'Fund Request Details')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>Fund Request Details</h1>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <p><strong>Amount:</strong> {{ $fundrequest->amount }}</p>
                <p><strong>Description:</strong> {{ $fundrequest->description }}</p>
                <x-ringlesoft-approval-actions :model="$fundrequest" />
            </div>
        </div>
    </div>
</div>
@endsection
