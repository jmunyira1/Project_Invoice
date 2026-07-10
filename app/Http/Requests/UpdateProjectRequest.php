<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RendersModalOnFailure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    use RendersModalOnFailure;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'quoted', 'active', 'completed', 'cancelled'])],
            'due_date' => ['nullable', 'date'],
        ];
    }

    protected function modalView(): string
    {
        return 'projects.partials.form_modal';
    }

    protected function modalData(): array
    {
        return [
            'project' => $this->route('project'),
            'clients' => auth()->user()->organisation->clients()->orderBy('name')->get(),
            'selectedClientId' => null,
        ];
    }
}
