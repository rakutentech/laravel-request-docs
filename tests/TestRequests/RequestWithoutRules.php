<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestRequests;

use Illuminate\Foundation\Http\FormRequest;

class RequestWithoutRules extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
    }
}
