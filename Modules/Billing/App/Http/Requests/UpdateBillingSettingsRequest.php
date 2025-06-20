<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-billing') ?? false;
    }

    public function rules(): array
    {
        return [
            'default_currency' => ['required', 'string', 'max:10'],
            'tax_rate' => ['required', 'numeric', 'between:0,100'],
            'email_templates' => ['nullable', 'string'],
        ];
    }
}
