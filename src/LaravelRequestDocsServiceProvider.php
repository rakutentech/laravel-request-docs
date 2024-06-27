<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\Commands\ExportRequestDocsCommand;
use Rakutentech\LaravelRequestDocs\Controllers\LaravelRequestDocsController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasCommand(ExportRequestDocsCommand::class)
            // ->hasAssets()
            ->hasViews();
        // ->hasAssets();
        // publish resources/dist/_astro to public/
        $this->publishes([
            __DIR__ . '/../resources/dist/_astro'     => public_path('request-docs/_astro'),
            __DIR__ . '/../resources/dist/index.html' => public_path('request-docs/index.html'),
        ], 'request-docs-assets');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        if (!config('request-docs.enabled')) {
            return;
        }

        // URL from which the docs will be served.
        Route::get(config('request-docs.url'), [LaravelRequestDocsController::class, 'index'])
            ->name('request-docs.index')
            ->middleware(config('request-docs.middlewares'));

        // Following url for api and assets, donot change to config one.
        Route::get("request-docs/api", [LaravelRequestDocsController::class, 'api'])
            ->name('request-docs.api')
            ->middleware(config('request-docs.middlewares'));

        Route::get("request-docs/config", [LaravelRequestDocsController::class, 'config'])
            ->name('request-docs.config')
            ->middleware(config('request-docs.middlewares'));

        Route::get("request-docs/_astro/{slug}", [LaravelRequestDocsController::class, 'assets'])
            // where slug is either js or css
            ->where('slug', '.*js|.*css|.*png|.*jpg|.*jpeg|.*gif|.*svg|.*ico|.*woff|.*woff2|.*ttf|.*eot|.*otf|.*map')
            ->name('request-docs.assets')
            ->middleware(config('request-docs.middlewares'));
    }
}
