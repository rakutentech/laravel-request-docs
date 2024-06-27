<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests;

use Illuminate\Foundation\Http\FormRequest;

class RequestWithoutRules extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
    }
}
