<?php

namespace Modules\Billing\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'billing_interval' => ['required', 'in:day,week,month,year'],
            'status' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('manage-billing') ?? false;
    }
}
