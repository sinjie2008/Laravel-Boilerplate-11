<?php

namespace Modules\EmailManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class EmailManagerController extends Controller
{
    /**
     * Display the Gmail settings form.
     */
    public function index()
    {
        $mailConfig = [
            'MAIL_MAILER' => env('MAIL_MAILER', 'smtp'),
            'MAIL_HOST' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'MAIL_PORT' => env('MAIL_PORT', 587),
            'MAIL_USERNAME' => env('MAIL_USERNAME'),
            'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION', 'tls'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME', 'Example'),
        ];

        return view('emailmanager::index', compact('mailConfig'));
    }

    /**
     * Update the Gmail settings.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'MAIL_MAILER' => 'required|string',
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|numeric',
            'MAIL_USERNAME' => 'nullable|string',
            'MAIL_PASSWORD' => 'nullable|string',
            'MAIL_ENCRYPTION' => 'required|string',
            'MAIL_FROM_ADDRESS' => 'required|email',
            'MAIL_FROM_NAME' => 'required|string',
        ]);

        $envFilePath = base_path('.env');
        $envContent = file_get_contents($envFilePath);

        $settings = $request->only([
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
        ]);

        foreach ($settings as $key => $value) {
            $envContent = preg_replace("/^{$key}=.*\n/m", "{$key}=\"{$value}\"\n", $envContent);
        }

        file_put_contents($envFilePath, $envContent);

        // Clear config cache to load new .env values
        Artisan::call('config:clear');

        return redirect()->route('admin.email-manager.index')->with('success', 'Gmail settings updated successfully.');
    }

    /**
     * Send a test email.
     */
    public function testSend(Request $request)
    {
        $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        try {
            // Temporarily set mailer config for testing
            Config::set('mail.mailer', $request->input('MAIL_MAILER', env('MAIL_MAILER')));
            Config::set('mail.host', $request->input('MAIL_HOST', env('MAIL_HOST')));
            Config::set('mail.port', $request->input('MAIL_PORT', env('MAIL_PORT')));
            Config::set('mail.username', $request->input('MAIL_USERNAME', env('MAIL_USERNAME')));
            Config::set('mail.password', $request->input('MAIL_PASSWORD', env('MAIL_PASSWORD')));
            Config::set('mail.encryption', $request->input('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION')));
            Config::set('mail.from.address', $request->input('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')));
            Config::set('mail.from.name', $request->input('MAIL_FROM_NAME', env('MAIL_FROM_NAME')));

            Mail::raw($request->input('body'), function ($message) use ($request) {
                $message->to($request->input('to_email'))
                        ->subject($request->input('subject'));
            });

            return redirect()->route('admin.email-manager.index')->with('success', 'Test email sent successfully!');
        } catch (\Exception $e) {
            Log::error('Test email failed: '.$e->getMessage());
            return redirect()->route('admin.email-manager.index')->with('error', 'Test email failed: '.$e->getMessage());
        }
    }

    // Remove unused methods
    public function create() { /* Removed */ }
    public function store(Request $request) { /* Removed */ }
    public function show($id) { /* Removed */ }
    public function edit($id) { /* Removed */ }
    public function destroy($id) { /* Removed */ }
}
