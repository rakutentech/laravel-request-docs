<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

class TelescopeController
{
    /**
     * For `config('request-docs.hide_matching')` test.
     */
    public function index(): int
    {
        return 1;
    }
}
