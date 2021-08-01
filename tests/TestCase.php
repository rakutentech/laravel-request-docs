<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelRequestDocsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
