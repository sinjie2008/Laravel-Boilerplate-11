<?php

namespace Modules\SettingsManager\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\SettingsManager\App\Models\Setting; // Import the Setting model

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = [
            'app_name' => $this->getSetting('app_name', config('app.name')),
            'system_logo' => $this->getSetting('system_logo', config('app.logo')),
            'favicon' => $this->getSetting('favicon', config('app.favicon')),
            'system_date_format' => $this->getSetting('system_date_format', config('app.date_format', 'Y-m-d')),
            'system_timezone' => $this->getSetting('system_timezone', config('app.timezone')),
            'default_language' => $this->getSetting('default_language', config('app.locale')),
            'system_email' => $this->getSetting('system_email', config('mail.from.address')),
            'support_phone' => $this->getSetting('support_phone', config('app.support_phone')),
            'company_address' => $this->getSetting('company_address', config('app.company_address')),
            'theme' => $this->getSetting('theme', config('app.theme', 'light')),
            'email_notifications_enabled' => $this->getSetting('email_notifications_enabled', config('app.email_notifications_enabled', true), 'boolean'),
            'sender_email_address' => $this->getSetting('sender_email_address', config('mail.from.address')),
            'maintenance_mode' => $this->getSetting('maintenance_mode', app()->isDownForMaintenance(), 'boolean'),
            'password_min_length' => $this->getSetting('password_min_length', config('auth.passwords.users.min_length', 8), 'integer'),
            'password_complexity' => $this->getSetting('password_complexity', config('auth.passwords.users.complexity', 'medium')),
        ];

        return view('settingsmanager::index', compact('settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $rules = [
            'app_name' => 'required|string|max:255',
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:2048',
            'system_date_format' => 'required|string|max:255',
            'system_timezone' => 'required|timezone',
            'default_language' => 'required|string|max:10',
            'system_email' => 'required|email|max:255',
            'support_phone' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'theme' => ['required', Rule::in(['light', 'dark'])],
            'email_notifications_enabled' => 'boolean',
            'sender_email_address' => 'required|email|max:255',
            'maintenance_mode' => 'boolean',
            'password_min_length' => 'required|integer|min:6',
            'password_complexity' => ['required', Rule::in(['none', 'low', 'medium', 'high'])],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file uploads and update settings
        $this->updateSetting('app_name', $request->input('app_name'));
        $this->updateSetting('system_date_format', $request->input('system_date_format'));
        $this->updateSetting('system_timezone', $request->input('system_timezone'));
        $this->updateSetting('default_language', $request->input('default_language'));
        $this->updateSetting('system_email', $request->input('system_email'));
        $this->updateSetting('support_phone', $request->input('support_phone'));
        $this->updateSetting('company_address', $request->input('company_address'));
        $this->updateSetting('theme', $request->input('theme'));
        $this->updateSetting('email_notifications_enabled', $request->boolean('email_notifications_enabled'), 'boolean');
        $this->updateSetting('sender_email_address', $request->input('sender_email_address'));
        $this->updateSetting('password_min_length', $request->input('password_min_length'), 'integer');
        $this->updateSetting('password_complexity', $request->input('password_complexity'));

        if ($request->hasFile('system_logo')) {
            $file = $request->file('system_logo');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'system_logo_' . time() . '.' . $extension;
            $logoPath = $file->storeAs('public/settings', $fileName);
            $this->updateSetting('system_logo', $logoPath, 'file'); // Store relative path
        }

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'favicon_' . time() . '.' . $extension;
            $faviconPath = $file->storeAs('public/settings', $fileName);
            $this->updateSetting('favicon', $faviconPath, 'file'); // Store relative path
        }

        // Handle maintenance mode
        if ($request->boolean('maintenance_mode') && !app()->isDownForMaintenance()) {
            Artisan::call('down');
        } elseif (!$request->boolean('maintenance_mode') && app()->isDownForMaintenance()) {
            Artisan::call('up');
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Helper to get a setting value from the database.
     *
     * @param string $key
     * @param mixed $default
     * @param string $type
     * @return mixed
     */
    private function getSetting(string $key, mixed $default = null, string $type = 'string'): mixed
    {
        $setting = Setting::where('key', $key)->first();

        if ($setting) {
            // The accessor in the Setting model handles casting
            return $setting->value;
        }

        return $default;
    }

    /**
     * Helper to update or create a setting in the database.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    private function updateSetting(string $key, mixed $value, string $type = 'string'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
