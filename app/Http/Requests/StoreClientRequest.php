<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RendersModalOnFailure;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    use RendersModalOnFailure;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'kra_pin' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function modalView(): string
    {
        return 'clients.partials.form_modal';
    }

    protected function modalData(): array
    {
        return ['client' => null];
    }
}
