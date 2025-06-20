<?php

namespace Modules\Billing\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:pending,paid,refunded'],
            'due_date' => ['required', 'date'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-billing') ?? false;
    }
}
