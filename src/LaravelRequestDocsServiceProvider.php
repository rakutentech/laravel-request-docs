<?php

namespace Rakutentech\LaravelRequestDocs;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Rakutentech\LaravelRequestDocs\Commands\LaravelRequestDocsCommand;
use Route;

class LaravelRequestDocsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-request-docs')
            ->hasConfigFile('request-docs')
            ->hasViews()
            ->hasAssets()
            ->hasCommand(LaravelRequestDocsCommand::class);
    }

    public function packageBooted()
    {
        parent::packageBooted();

        Route::get(config('request-docs.url'), [\Rakutentech\LaravelRequestDocs\Controllers\LaravelRequestDocsController::class, 'index'])
            ->name('request-docs.index')
            ->middleware(config('request-docs.middlewares'));
    }
}
