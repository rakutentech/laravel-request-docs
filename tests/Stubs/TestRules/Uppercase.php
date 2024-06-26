<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRules;

use Illuminate\Contracts\Validation\Rule;

class Uppercase implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value)
    {
        return strtoupper($value) === $value;
    }

    /**
     * Get the validation error message.
     */
    public function message()
    {
        return 'The :attribute must be uppercase.';
    }
}
