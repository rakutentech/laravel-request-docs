<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\NotFoundWhenProduction;

class NotFoundWhenProductionTest extends TestCase
{
    public function testForbiddenInProduction(): void
    {
        foreach (['prod', 'production'] as $production) {
            app()['env'] = $production;
            Route::get('middleware', static fn () => 1)->middleware(NotFoundWhenProduction::class);

            $this->get('middleware')
                ->assertStatus(403)
                ->assertExactJson(['status' => 'forbidden', 'status_code' => 403]);
        }
    }

    public function testHandle(): void
    {
        Route::get('middleware', static fn () => response()->json([1]))->middleware(NotFoundWhenProduction::class);

        $this->get('middleware')
            ->assertStatus(200)
            ->assertExactJson([1]);
    }
}
