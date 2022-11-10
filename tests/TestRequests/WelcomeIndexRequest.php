<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestRequests;

use Illuminate\Foundation\Http\FormRequest;
use Rakutentech\LaravelRequestDocs\Tests\TestRules\Uppercase;

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
            'name'            => ['nullable', 'string', 'min:5', 'max:255'],
            'title'           => new Uppercase(),
            'file'          => 'file',
            'image'         => 'image',
            'page'            => 'nullable|integer|min:1',
            'per_page'        => 'nullable|integer|min:1|max:100',
        ];
    }
}
