<?php

namespace Modules\EmailManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Modules\EmailManager\app\Emails\TestEmail; // Corrected namespace
use Modules\EmailManager\App\Models\EmailSetting; // Import the model
use Exception;
use Illuminate\Support\Facades\Log; // Use Log facade
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt; // For password encryption/decryption

class EmailManagerController extends Controller
{
    /**
     * Display the email settings page.
     */
    public function index()
    {
        // Fetch settings from DB, or create default if none exist
        $settings = EmailSetting::firstOrCreate([], [
            'mailer' => config('mail.default', 'smtp'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password') ? Crypt::encryptString(config('mail.mailers.smtp.password')) : null, // Encrypt if exists
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ]);

        // No need to decrypt password for the view, the view handles the placeholder correctly.

        return view('emailmanager::index', compact('settings'));
    }

    /**
     * Update the email settings in the database.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mailer' => 'required|string|in:smtp,sendmail,log,array',
            'host' => 'nullable|required_if:mailer,smtp|string',
            'port' => 'nullable|required_if:mailer,smtp|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string', // Password can be empty if not changed
            'encryption' => 'nullable|required_if:mailer,smtp|string|in:null,tls,ssl',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
        ]);

        try {
            $settings = EmailSetting::firstOrNew(['id' => $request->input('setting_id', 1)]); // Assuming ID 1 or first record

            $settings->mailer = $validated['mailer'];
            $settings->host = $validated['host'];
            $settings->port = $validated['port'];
            $settings->username = $validated['username'];
            $settings->encryption = $validated['encryption'];
            $settings->from_address = $validated['from_address'];
            $settings->from_name = $validated['from_name'];

            // Only update password if a new one is provided
            if (!empty($validated['password'])) {
                 // Encrypt the password before saving
                $settings->password = Crypt::encryptString($validated['password']);
            } elseif (empty($validated['password']) && $settings->exists && $request->filled('password')) {
                 // If password field was submitted but empty, clear it
                 $settings->password = null;
            }
            // If password field is not submitted or empty, and it's a new record, it remains null
            // If password field is not submitted or empty, and it's an existing record, the old password remains

            $settings->save();

            // Optionally clear config cache if other parts of the app rely on config('mail...')
            // Artisan::call('config:clear');
            // Artisan::call('config:cache');

            return redirect()->route('admin.email-manager.index')->with('success', 'Email settings updated successfully.');

        } catch (Exception $e) {
            Log::error('Failed to update email settings: ' . $e->getMessage());
            return redirect()->route('admin.email-manager.index')->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    /**
     * Send a test email using database settings.
     */
    public function sendTestEmail(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'test_email_recipient' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.email-manager.index')
                        ->withErrors($validator, 'test_email')
                        ->withInput();
        }

        $recipient = $request->input('test_email_recipient');
        $settings = EmailSetting::first(); // Get the current settings

        if (!$settings) {
            return redirect()->route('admin.email-manager.index')
                           ->with('test_email_error', 'Email settings not configured yet.')
                           ->withInput();
        }

        try {
            // Decrypt password for configuration
            $decryptedPassword = null;
            if ($settings->password) {
                try {
                    $decryptedPassword = Crypt::decryptString($settings->password);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    Log::error('Failed to decrypt password for sending test email.', ['setting_id' => $settings->id]);
                    throw new Exception('Could not decrypt stored password.');
                }
            }


            // Dynamically configure the mailer for this specific email sending attempt
            $mailerName = 'dynamic_mailer_' . uniqid(); // Unique name for temporary mailer config
            Config::set("mail.mailers.{$mailerName}", [
                'transport' => $settings->mailer, // Use 'smtp', 'sendmail' etc. from DB
                'host' => $settings->host,
                'port' => $settings->port,
                'encryption' => $settings->encryption ?: null, // Handle 'null' string or actual null
                'username' => $settings->username,
                'password' => $decryptedPassword,
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'), // Optional: Get from env if needed
            ]);
             Config::set('mail.from.address', $settings->from_address);
             Config::set('mail.from.name', $settings->from_name);


            // Send using the dynamically configured mailer
            Mail::mailer($mailerName)->to($recipient)->send(new TestEmail());

            return redirect()->route('admin.email-manager.index')->with('test_email_success', 'Test email sent successfully to ' . $recipient . ' using ' . $settings->mailer);

        } catch (Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('admin.email-manager.index')
                           ->with('test_email_error', 'Failed to send test email: ' . $e->getMessage())
                           ->withInput();
        }
        // No need for finally block to cache config as we used a temporary mailer config
    }

    // Removed the updateEnv method as it's no longer needed
}
