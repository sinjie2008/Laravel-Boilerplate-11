@extends('adminlte::page')

@section('content_header')
    <h1>Email Manager Settings</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Gmail Settings</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.email-manager.update', ['email_manager' => 1]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="MAIL_MAILER">Mail Mailer</label>
                                <input type="text" class="form-control" id="MAIL_MAILER" name="MAIL_MAILER" value="{{ $mailConfig['MAIL_MAILER'] }}" required>
                            </div>

                            <div class="form-group">
                                <label for="MAIL_HOST">Mail Host</label>
                                <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST" value="{{ $mailConfig['MAIL_HOST'] }}" required>
                            </div>

                            <div class="form-group">
                                <label for="MAIL_PORT">Mail Port</label>
                                <input type="number" class="form-control" id="MAIL_PORT" name="MAIL_PORT" value="{{ $mailConfig['MAIL_PORT'] }}" required>
                            </div>

                            <div class="form-group">
                                <label for="MAIL_USERNAME">Mail Username</label>
                                <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" value="{{ $mailConfig['MAIL_USERNAME'] }}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_PASSWORD">Mail Password</label>
                                <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" value="{{ $mailConfig['MAIL_PASSWORD'] }}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_ENCRYPTION">Mail Encryption</label>
                                <input type="text" class="form-control" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" value="{{ $mailConfig['MAIL_ENCRYPTION'] }}" required>
                            </div>

                            <div class="form-group">
                                <label for="MAIL_FROM_ADDRESS">Mail From Address</label>
                                <input type="email" class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" value="{{ $mailConfig['MAIL_FROM_ADDRESS'] }}" required>
                            </div>

                            <div class="form-group">
                                <label for="MAIL_FROM_NAME">Mail From Name</label>
                                <input type="text" class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" value="{{ $mailConfig['MAIL_FROM_NAME'] }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Test Email</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.email-manager.test-send') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="to_email">To Email</label>
                                <input type="email" class="form-control" id="to_email" name="to_email" required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" value="Test Email from Laravel" required>
                            </div>
                            <div class="form-group">
                                <label for="body">Body</label>
                                <textarea class="form-control" id="body" name="body" rows="5" required>This is a test email sent from the Laravel Email Manager.</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Send Test Email</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
