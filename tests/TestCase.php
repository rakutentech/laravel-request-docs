<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsServiceProvider;
use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->registerRoutes();
    }

    public function registerRoutes(): void
    {
        Route::get('/', [TestControllers\WelcomeController::class, 'index']);
        Route::get('welcome', [TestControllers\WelcomeController::class, 'index']);
        Route::get('welcome/{id}', [TestControllers\WelcomeController::class, 'show']);
        Route::post('welcome', [TestControllers\WelcomeController::class, 'store'])->middleware('auth:api');
        Route::put('welcome', 'Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers\WelcomeController@edit');
        Route::patch('welcome/patch', 'Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers\WelcomeController@edit');
        Route::delete('welcome', [TestControllers\WelcomeController::class, 'destroy']);
        Route::get('health', [TestControllers\WelcomeController::class, 'health']);
        Route::get('single', TestControllers\SingleActionController::class);
        Route::delete('welcome/no-rules', [TestControllers\WelcomeController::class, 'noRules']);
        Route::post('comments-on-request-rules-method', [TestControllers\CommentsOnRequestRulesMethodController::class, 'index']);

        Route::get('closure', static fn () => true);

        Route::apiResource('accounts', TestControllers\AccountController::class);

        Route::match(['get', 'post'], 'match', [TestControllers\MatchController::class, 'index']);

        // Test duplication
        Route::apiResource('accounts', TestControllers\AccountController::class);

        // Expected to be skipped
        Route::get('telescope', [TestControllers\TelescopeController::class, 'index']);

        Route::options('options_is_not_included', static fn () => false);
    }

    /**
     * @inheritDoc
     */
    protected function getEnvironmentSetUp($app)
    {
        app()->setBasePath(__DIR__ . '/../');

        $app['config']->set('database.default', 'testing');
        $app['config']->set('app.debug', true);
    }

    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelRequestDocsServiceProvider::class,
        ];
    }

    protected function countRoutesWithLRDDoc(): int
    {
        return count(Route::getRoutes()->getRoutes()) - 2; // Exclude `telescope`, `request-docs`
    }
}
