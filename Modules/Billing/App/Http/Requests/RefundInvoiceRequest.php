<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-billing') ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => ['nullable', 'numeric', 'min:0.5'],
        ];
    }
}
