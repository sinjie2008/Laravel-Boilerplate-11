@extends('adminlte::page') {{-- Use AdminLTE layout --}}

@section('plugins.BsCustomFileInput', true) {{-- Load plugin for styled file input --}}

@section('content_header')
    <h1>Settings Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Application Settings</h3>
        </div>
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-check"></i> Success!</h5>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Site Name --}}
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" name="site_name" id="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}">
                </div>

                {{-- Site Description --}}
                <div class="form-group">
                    <label for="site_description">Site Description</label>
                    <textarea name="site_description" id="site_description" class="form-control" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                </div>

                {{-- Site Logo --}}
                <div class="form-group">
                    <label for="site_logo">Site Logo</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="site_logo" name="site_logo">
                            <label class="custom-file-label" for="site_logo">Choose file</label>
                        </div>
                    </div>
                    @if(isset($settings['site_logo']) && $settings['site_logo'])
                        <div class="mt-2">
                            <img src="{{ asset($settings['site_logo']) }}" alt="Current Logo" style="max-height: 50px;">
                            <small class="form-text text-muted">Current Logo: {{ $settings['site_logo'] }}</small>
                        </div>
                    @endif
                     <small class="form-text text-muted">Leave blank to keep the current logo.</small>
                </div>

                {{-- Timezone --}}
                <div class="form-group">
                    <label for="timezone">Timezone</label>
                    {{-- You might want to replace this with a select dropdown populated with timezones --}}
                    <input type="text" name="timezone" id="timezone" class="form-control" value="{{ old('timezone', $settings['timezone'] ?? 'UTC') }}">
                     <small class="form-text text-muted">Example: Asia/Singapore, America/New_York, UTC</small>
                </div>

                {{-- Locale --}}
                <div class="form-group">
                    <label for="locale">Locale</label>
                     {{-- You might want to replace this with a select dropdown populated with available locales --}}
                    <input type="text" name="locale" id="locale" class="form-control" value="{{ old('locale', $settings['locale'] ?? 'en') }}">
                     <small class="form-text text-muted">Example: en, es, fr</small>
                </div>

                {{-- Maintenance Mode --}}
                 <div class="form-group">
                    <label for="maintenance_mode">Maintenance Mode</label>
                    <select name="maintenance_mode" id="maintenance_mode" class="form-control">
                        <option value="true" {{ (old('maintenance_mode', $settings['maintenance_mode'] ?? 'false') == 'true') ? 'selected' : '' }}>Enabled</option>
                        <option value="false" {{ (old('maintenance_mode', $settings['maintenance_mode'] ?? 'false') == 'false') ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                {{-- Company Email --}}
                <div class="form-group">
                    <label for="company_email">Company Email</label>
                    <input type="email" name="company_email" id="company_email" class="form-control" value="{{ old('company_email', $settings['company_email'] ?? '') }}">
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
@stop

@section('css')
    {{-- Add custom CSS if needed --}}
@stop

@push('js')
<script>
    // Script to show filename in custom file input is automatically handled by BsCustomFileInput plugin
    // $(document).ready(function () {
    //     bsCustomFileInput.init(); // No longer needed here as the plugin handles it
    // });
</script>
@endpush
