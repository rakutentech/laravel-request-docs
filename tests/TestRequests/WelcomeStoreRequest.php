<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestRequests;

use Illuminate\Foundation\Http\FormRequest;

class WelcomeStoreRequest extends FormRequest
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
            'message_param' => 'nullable|string',
        ];
    }
}
