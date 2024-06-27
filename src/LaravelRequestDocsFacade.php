<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 * @see \Rakutentech\LaravelRequestDocs\LaravelRequestDocs
 */
class LaravelRequestDocsFacade extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return LaravelRequestDocs::class;
    }
}
