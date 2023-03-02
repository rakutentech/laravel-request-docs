<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\NotFoundWhenProduction;

class NotFoundWhenProductionTest extends TestCase
{
    public function testForbiddenInProduction()
    {
        foreach (['prod', 'production'] as $production) {
            app()['env'] = $production;
            Route::get('middleware', function () {
                return 1;
            })->middleware(NotFoundWhenProduction::class);

            $this->get('middleware')
                ->assertStatus(403)
                ->assertExactJson(['status' => 'forbidden', 'status_code' => 403]);
        }
    }

    public function testHandle()
    {
        Route::get('middleware', function () {
            return 1;
        })->middleware(NotFoundWhenProduction::class);

        $this->get('middleware')
            ->assertStatus(200)
            ->assertExactJson([1]);
    }
}
