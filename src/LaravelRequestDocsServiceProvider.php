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
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(LaravelRequestDocsCommand::class);

        Route::get('/request-docs', [\Rakutentech\LaravelRequestDocs\Controllers\LaravelRequestDocsController::class, 'index'])
            ->name('request-docs.index')
            ->middleware(config('request-docs.middlewares'));
    }
}
