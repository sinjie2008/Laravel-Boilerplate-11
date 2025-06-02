@extends('adminlte::page')

@section('title', 'Edit Subscription')

@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)

@section('content_header')
    <h1>Edit Subscription for: {{ $subscription->user->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Subscription Details</h3>
        </div>
        <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" data-placeholder="Select User" style="width: 100%;" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $subscription->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="plan_id">Plan</label>
                    <select name="plan_id" id="plan_id" class="form-control select2 @error('plan_id') is-invalid @enderror" data-placeholder="Select Plan" style="width: 100%;" required>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} (Price: {{ number_format($plan->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <x-adminlte-input-date name="starts_at" label="Starts At" :config="['format' => 'YYYY-MM-DD HH:mm:ss']" placeholder="Choose a start date..." value="{{ old('starts_at', $subscription->starts_at ? $subscription->starts_at->format('Y-m-d H:i:s') : '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-date>
                @error('starts_at')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror

                <x-adminlte-input-date name="ends_at" label="Ends At (Optional)" :config="['format' => 'YYYY-MM-DD HH:mm:ss']" placeholder="Choose an end date..." value="{{ old('ends_at', $subscription->ends_at ? $subscription->ends_at->format('Y-m-d H:i:s') : '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-date>
                @error('ends_at')
                     <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ old('status', $subscription->status) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Subscription</button>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@stop