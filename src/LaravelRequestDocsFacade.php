<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 * @see \Rakutentech\LaravelRequestDocs\LaravelRequestDocs
 */
class LaravelRequestDocsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-request-docs';
    }
}
