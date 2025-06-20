@extends('adminlte::page')

@section('content_header')
    <h1>Billing Settings</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @can('manage-billing')
                <form method="POST" action="{{ route('admin.billing.settings.update') }}">
                    @csrf
                <div class="form-group">
                    <label for="default_currency">Default Currency</label>
                    <input type="text" class="form-control" id="default_currency" name="default_currency" value="{{ old('default_currency', $settings['default_currency']) }}">
                </div>
                <div class="form-group">
                    <label for="tax_rate">Tax Rate (%)</label>
                    <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}">
                </div>
                <div class="form-group">
                    <label for="email_templates">Email Templates</label>
                    <textarea class="form-control" id="email_templates" name="email_templates" rows="5">{{ old('email_templates', $settings['email_templates']) }}</textarea>
                </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            @endcan
        </div>
    </div>
@endsection
