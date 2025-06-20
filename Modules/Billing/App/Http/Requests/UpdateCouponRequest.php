<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'amount_off' => ['nullable', 'numeric', 'min:0'],
            'percent_off' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'duration' => ['required', 'string'],
            'applies_to' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('manage-billing') ?? false;
    }
}
