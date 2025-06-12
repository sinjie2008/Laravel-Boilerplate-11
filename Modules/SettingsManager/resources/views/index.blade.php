@extends('adminlte::page')

@section('content_header')
    <h1>System Settings</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">System Settings</h3>
        </div>
                    <form action="{{ route('admin.settings-manager.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Branding --}}
                            <div class="form-group">
                                <label for="app_name">Application Name</label>
                                <input type="text" name="app_name" class="form-control" id="app_name" value="{{ old('app_name', $settings['app_name']) }}">
                            </div>
                            <div class="form-group">
                                <label for="system_logo">System Logo</label>
                                <input type="file" name="system_logo" class="form-control" id="system_logo">
                                @if ($settings['system_logo'])
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($settings['system_logo']) }}" alt="System Logo" style="max-width: 150px; height: auto;">
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="favicon">Favicon</label>
                                <input type="file" name="favicon" class="form-control" id="favicon">
                                @if ($settings['favicon'])
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($settings['favicon']) }}" alt="Favicon" style="max-width: 50px; height: auto;">
                                    </div>
                                @endif
                            </div>

                            <hr>

                            {{-- General Settings --}}
                            <div class="form-group">
                                <label for="system_date_format">System Date Format</label>
                                <input type="text" name="system_date_format" class="form-control" id="system_date_format" value="{{ old('system_date_format', $settings['system_date_format']) }}">
                            </div>
                            <div class="form-group">
                                <label for="system_timezone">System Timezone</label>
                                <input type="text" name="system_timezone" class="form-control" id="system_timezone" value="{{ old('system_timezone', $settings['system_timezone']) }}">
                            </div>
                            <div class="form-group">
                                <label for="default_language">Default Language/Locale</label>
                                <input type="text" name="default_language" class="form-control" id="default_language" value="{{ old('default_language', $settings['default_language']) }}">
                            </div>

                            <hr>

                            {{-- Contact Information --}}
                            <div class="form-group">
                                <label for="system_email">System Email Address</label>
                                <input type="email" name="system_email" class="form-control" id="system_email" value="{{ old('system_email', $settings['system_email']) }}">
                            </div>
                            <div class="form-group">
                                <label for="support_phone">Support Phone Number</label>
                                <input type="text" name="support_phone" class="form-control" id="support_phone" value="{{ old('support_phone', $settings['support_phone']) }}">
                            </div>
                            <div class="form-group">
                                <label for="company_address">Company Address</label>
                                <textarea name="company_address" class="form-control" id="company_address">{{ old('company_address', $settings['company_address']) }}</textarea>
                            </div>

                            <hr>

                            {{-- Appearance --}}
                            <div class="form-group">
                                <label for="theme">Theme</label>
                                <select name="theme" id="theme" class="form-control">
                                    <option value="light" {{ old('theme', $settings['theme']) == 'light' ? 'selected' : '' }}>Light</option>
                                    <option value="dark" {{ old('theme', $settings['theme']) == 'dark' ? 'selected' : '' }}>Dark</option>
                                </select>
                            </div>

                            <hr>

                            {{-- Notifications --}}
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="email_notifications_enabled" class="custom-control-input" id="email_notifications_enabled" value="1" {{ old('email_notifications_enabled', $settings['email_notifications_enabled']) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_notifications_enabled">Enable Email Notifications</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sender_email_address">Sender Email Address for System Emails</label>
                                <input type="email" name="sender_email_address" class="form-control" id="sender_email_address" value="{{ old('sender_email_address', $settings['sender_email_address']) }}">
                            </div>

                            <hr>

                            {{-- Security --}}
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="maintenance_mode" class="custom-control-input" id="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="maintenance_mode">Enable Maintenance Mode</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_min_length">Password Minimum Length</label>
                                <input type="number" name="password_min_length" class="form-control" id="password_min_length" value="{{ old('password_min_length', $settings['password_min_length']) }}">
                            </div>
                            <div class="form-group">
                                <label for="password_complexity">Password Complexity</label>
                                <select name="password_complexity" id="password_complexity" class="form-control">
                                    <option value="none" {{ old('password_complexity', $settings['password_complexity']) == 'none' ? 'selected' : '' }}>None</option>
                                    <option value="low" {{ old('password_complexity', $settings['password_complexity']) == 'low' ? 'selected' : '' }}>Low (e.g., uppercase, lowercase)</option>
                                    <option value="medium" {{ old('password_complexity', $settings['password_complexity']) == 'medium' ? 'selected' : '' }}>Medium (e.g., uppercase, lowercase, numbers)</option>
                                    <option value="high" {{ old('password_complexity', $settings['password_complexity']) == 'high' ? 'selected' : '' }}>High (e.g., uppercase, lowercase, numbers, symbols)</option>
                                </select>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // No custom JS needed for Bootstrap 5 default file input
        });
    </script>
@endpush
