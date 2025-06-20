<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Billing\App\Http\Requests\UpdateBillingSettingsRequest;
use Modules\SettingsManager\App\Models\Setting;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'default_currency' => Setting::where('key', 'billing.default_currency')->value('value') ?? 'USD',
            'tax_rate' => Setting::where('key', 'billing.tax_rate')->value('value') ?? '0',
            'email_templates' => Setting::where('key', 'billing.email_templates')->value('value') ?? '',
        ];

        return view('billing::settings.index', compact('settings'));
    }

    public function update(UpdateBillingSettingsRequest $request): RedirectResponse
    {
        Setting::updateOrCreate(
            ['key' => 'billing.default_currency'],
            ['value' => $request->input('default_currency')]
        );
        Setting::updateOrCreate(
            ['key' => 'billing.tax_rate'],
            ['value' => $request->input('tax_rate')]
        );
        Setting::updateOrCreate(
            ['key' => 'billing.email_templates'],
            [
                'value' => $request->input('email_templates'),
                'type' => 'text',
            ]
        );

        activity()
            ->causedBy($request->user())
            ->withProperties($request->validated())
            ->log('updated billing settings');

        return back()->with('success', 'Billing settings updated');
    }
}
