<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsServiceProvider;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocs;
use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsToOpenApi;
use Rakutentech\LaravelRequestDocs\Tests\TestControllers;

class TestCase extends Orchestra
{
    protected LaravelRequestDocs $lrd;
    public function setUp(): void
    {
        parent::setUp();
        $this->lrd          = new LaravelRequestDocs();
        $this->lrdToOpenApi = new LaravelRequestDocsToOpenApi();
        $this->registerRoutes();
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

    public function registerRoutes() {
        Route::get('/', [TestControllers\WelcomeController::class, 'index']);
        Route::get('welcome', [TestControllers\WelcomeController::class, 'index']);
        Route::post('welcome', [TestControllers\WelcomeController::class, 'store'])->middleware('auth:api');
        Route::put('welcome', [TestControllers\WelcomeController::class, 'edit']);
        Route::delete('welcome', [TestControllers\WelcomeController::class, 'destroy']);
        Route::get('single', TestControllers\SingleActionController::class);

        // Expected to be skipped
        Route::get('telescope', [TestControllers\TelescopeController::class, 'index']);
    }

    protected function countRoutesWithLRDDoc(): int
    {
        return count(Route::getRoutes()) - 2; // Exclude `telescope`, `request-docs`
    }
}
