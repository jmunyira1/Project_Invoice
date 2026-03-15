<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'document_id' => ['nullable', 'integer', 'exists:documents,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(['mpesa', 'cash', 'bank', 'cheque', 'card'])],
            'reference' => ['nullable', 'string', 'max:100'],
            'paid_on' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
