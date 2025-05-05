@extends('adminlte::page')

@section('title', 'Email Settings')

@section('content_header')
    <h1>Google Email Settings</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Configure Email Settings</h3>
                </div>
                {{-- Display success/error messages for settings update --}}
                @if(session('success'))
                    <div class="alert alert-success m-2">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger m-2">
                        {{ session('error') }}
                    </div>
                @endif
                 @if ($errors->any() && !$errors->hasBag('test_email')) {{-- Show general errors only if not test email errors --}}
                    <div class="alert alert-danger m-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.email-manager.update') }}" method="POST">
                    @csrf
                    {{-- Hidden field for the settings ID --}}
                    {{-- Use the actual ID if settings exist, otherwise, it will be handled by firstOrCreate/firstOrNew in controller --}}
                    <input type="hidden" name="setting_id" value="{{ $settings->id ?? '' }}">

                    <div class="card-body">
                        {{-- Mailer --}}
                        {{-- Mailer --}}
                        <div class="form-group">
                            <label for="mailer">Mailer</label>
                            <select name="mailer" id="mailer" class="form-control @error('mailer') is-invalid @enderror">
                                {{-- Default to smtp if no setting exists --}}
                                <option value="smtp" {{ old('mailer', $settings->mailer ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ old('mailer', $settings->mailer ?? 'smtp') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="log" {{ old('mailer', $settings->mailer ?? 'smtp') == 'log' ? 'selected' : '' }}>Log</option>
                                <option value="array" {{ old('mailer', $settings->mailer ?? 'smtp') == 'array' ? 'selected' : '' }}>Array</option>
                                {{-- Add other mailers if needed --}}
                            </select>
                             @error('mailer') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        {{-- SMTP Settings (Show only if SMTP is selected) --}}
                        {{-- Default to showing SMTP if no setting exists or if it's SMTP --}}
                        <div id="smtp_settings" style="{{ old('mailer', $settings->mailer ?? 'smtp') == 'smtp' ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="host">Host</label>
                                {{-- Default to Gmail host if no setting exists --}}
                                <input type="text" name="host" id="host" class="form-control @error('host') is-invalid @enderror" value="{{ old('host', $settings->host ?? 'smtp.gmail.com') }}" placeholder="e.g., smtp.gmail.com">
                                @error('host') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="port">Port</label>
                                {{-- Default to Gmail TLS port if no setting exists --}}
                                <input type="number" name="port" id="port" class="form-control @error('port') is-invalid @enderror" value="{{ old('port', $settings->port ?? 587) }}" placeholder="e.g., 587">
                                @error('port') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $settings->username ?? '') }}" placeholder="e.g., your.email@gmail.com">
                                @error('username') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password / App Password</label>
                                {{-- Always show placeholder, never show saved password value --}}
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current password">
                                <small class="form-text text-muted">Enter a new password or App Password (for Gmail/Google Workspace) to update it. Leave blank to keep the existing one.</small>
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="encryption">Encryption</label>
                                <select name="encryption" id="encryption" class="form-control @error('encryption') is-invalid @enderror">
                                    {{-- Default to tls if no setting exists --}}
                                    <option value="null" {{ old('encryption', $settings->encryption ?? 'tls') == 'null' ? 'selected' : '' }}>None</option>
                                    <option value="tls" {{ old('encryption', $settings->encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('encryption', $settings->encryption ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                                @error('encryption') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- From Address --}}
                        <div class="form-group">
                            <label for="from_address">From Address</label>
                            <input type="email" name="from_address" id="from_address" class="form-control @error('from_address') is-invalid @enderror" value="{{ old('from_address', $settings->from_address ?? '') }}" placeholder="e.g., noreply@example.com">
                             @error('from_address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        {{-- From Name --}}
                        <div class="form-group">
                            <label for="from_name">From Name</label>
                            <input type="text" name="from_name" id="from_name" class="form-control @error('from_name') is-invalid @enderror" value="{{ old('from_name', $settings->from_name ?? config('app.name', 'Laravel')) }}" placeholder="e.g., Your Application Name">
                             @error('from_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Send Test Email</h3>
                </div>
                <form action="{{ route('admin.email-manager.send-test-email') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if(session('test_email_success'))
                            <div class="alert alert-success">
                                {{ session('test_email_success') }}
                            </div>
                        @endif
                        @if(session('test_email_error'))
                            <div class="alert alert-danger">
                                {{ session('test_email_error') }}
                            </div>
                        @endif
                        {{-- Display validation errors specifically for the test email form --}}
                        @if ($errors->test_email->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->test_email->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="test_email_recipient">Recipient Email</label>
                            <input type="email" name="test_email_recipient" id="test_email_recipient" class="form-control @error('test_email_recipient', 'test_email') is-invalid @enderror" placeholder="Enter recipient email address" required value="{{ old('test_email_recipient', auth()->user()->email ?? '') }}">
                             @error('test_email_recipient', 'test_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-secondary">Send Test Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mailerSelect = document.getElementById('mailer');
        const smtpSettingsDiv = document.getElementById('smtp_settings');
        const hostInput = document.getElementById('host');
        const portInput = document.getElementById('port');
        const encryptionSelect = document.getElementById('encryption');

        function toggleSmtpSettings() {
            const isSmtp = mailerSelect.value === 'smtp';
            smtpSettingsDiv.style.display = isSmtp ? 'block' : 'none';

            // Set required attribute based on visibility for HTML5 validation
            const smtpInputs = smtpSettingsDiv.querySelectorAll('input, select');
            smtpInputs.forEach(input => {
                // Password is not strictly required if already set
                if (input.name !== 'password') {
                     input.required = isSmtp;
                }
            });

             // Suggest defaults when switching TO smtp and fields are empty
             if (isSmtp) {
                if (!hostInput.value) hostInput.placeholder = 'e.g., smtp.gmail.com';
                if (!portInput.value) portInput.placeholder = 'e.g., 587';
                // You might want to pre-select TLS if encryption is empty
                // if (!encryptionSelect.value || encryptionSelect.value === 'null') {
                //    encryptionSelect.value = 'tls';
                // }
            }
        }

        mailerSelect.addEventListener('change', toggleSmtpSettings);

        // Initial check on page load
        toggleSmtpSettings();
    });
</script>
@stop
