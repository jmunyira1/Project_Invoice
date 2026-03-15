<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeliverableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'template_id' => ['required', 'integer', 'exists:templates,id'],
            'type' => ['required', Rule::in(['quote', 'invoice', 'receipt', 'delivery_note', 'statement'])],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
