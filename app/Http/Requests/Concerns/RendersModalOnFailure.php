<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\ViewErrorBag;

/**
 * When a form request is submitted from an HTMX modal, a normal validation
 * failure (redirect back) would break the in-modal experience. Instead we
 * re-render the modal form partial with the errors so HTMX can swap it back
 * into the open modal.
 *
 * Implementing requests must provide modalView() and modalData().
 */
trait RendersModalOnFailure
{
    abstract protected function modalView(): string;

    abstract protected function modalData(): array;

    protected function failedValidation(Validator $validator)
    {
        if ($this->header('HX-Request')) {
            $bag = new ViewErrorBag;
            $bag->put('default', $validator->getMessageBag());

            $html = view($this->modalView(), array_merge($this->modalData(), [
                'errors' => $bag,
            ]))->render();

            throw new HttpResponseException(response($html, 200));
        }

        parent::failedValidation($validator);
    }
}
