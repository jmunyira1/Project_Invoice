<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RendersModalOnFailure;
use App\Models\Template;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    use RendersModalOnFailure;

    public function authorize(): bool
    {
        return true; // Auth is handled in the controller
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

    protected function modalView(): string
    {
        return 'documents.partials.form_modal';
    }

    protected function modalData(): array
    {
        $org = auth()->user()->organisation;

        return [
            'projects' => $org->projects()->with('client')->orderBy('title')->get(),
            'templates' => Template::all(),
            'selectedProjectId' => $this->input('project_id'),
            'defaultTemplateId' => $this->input('template_id') ?? $org->default_template_id,
        ];
    }
}
