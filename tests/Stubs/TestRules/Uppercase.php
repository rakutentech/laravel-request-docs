<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRules;

use Illuminate\Contracts\Validation\Rule;

class Uppercase implements Rule
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value)
    {
        return strtoupper($value) === $value;
    }

    /**
     * @inheritDoc
     * @return string|string[]
     */
    public function message()
    {
        return 'The :attribute must be uppercase.';
    }
}
