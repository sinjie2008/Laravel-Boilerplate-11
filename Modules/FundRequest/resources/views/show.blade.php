@extends('adminlte::page')

@section('content')
    <h1>Fund Request Details</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>User:</strong> {{ $fundRequest->user->name }}</p>
            <p><strong>Amount:</strong> {{ $fundRequest->amount }}</p>
            <p><strong>Purpose:</strong> {{ $fundRequest->purpose }}</p>
            <p><strong>Status:</strong> {{ $fundRequest->status }}</p>
            @if($fundRequest->rejection_reason)
                <p><strong>Rejection Reason:</strong> {{ $fundRequest->rejection_reason }}</p>
            @endif
        </div>
    </div>
@endsection
