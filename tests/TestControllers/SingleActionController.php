<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestControllers;

/**
 * Single action controller with a single `__invoke` method
 *
 * @see https://laravel.com/docs/controllers#single-action-controllers
 */
class SingleActionController
{
    public function __invoke()
    {
        return 1;
    }
}
