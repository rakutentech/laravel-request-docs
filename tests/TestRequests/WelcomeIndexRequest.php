<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestRequests;

use Illuminate\Foundation\Http\FormRequest;

class WelcomeIndexRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page'            => 'nullable|integer|min:1',
            'per_page'        => 'nullable|integer|min:1|max:100',
        ];
    }
}
