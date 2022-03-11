<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocs;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsServiceProvider;

class TestCase extends Orchestra
{

    protected LaravelRequestDocs $lrd;

    public function setUp(): void
    {
        parent::setUp();
        $this->lrd = new LaravelRequestDocs();
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

    protected function getControllerInfoByUri(string $uri): ?array
    {
        return collect($this->lrd->getControllersInfo())
            ->where('uri', '=', $uri)
            ->first();
    }
}
