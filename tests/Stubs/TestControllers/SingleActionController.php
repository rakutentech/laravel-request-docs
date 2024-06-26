<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

/**
 * Single action controller with a single `__invoke` method
 *
 * @see https://laravel.com/docs/controllers#single-action-controllers
 */
class SingleActionController
{
    public function __invoke(): int
    {
        return 1;
    }
}
