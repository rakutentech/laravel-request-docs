<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsMiddleware;

class LaravelRequestDocsMiddlewareTest extends TestCase
{
    public function testMissingLRDHeader(): void
    {
        Route::get('middleware', static fn () => ['test' => true])->middleware(LaravelRequestDocsMiddleware::class);

        $this->get('middleware')
            ->assertStatus(200)
            ->assertExactJson(['test' => true]);
    }

    public function testNotJsonResponse(): void
    {
        Route::get('middleware', static fn () => 1)->middleware(LaravelRequestDocsMiddleware::class);

        $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200)
            ->assertExactJson([1]);
    }

    public function testJsonResponseIsObject(): void
    {
        Route::get('middleware', static fn () => response()->json(['test' => true]))->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = new Collection($response->json());

        $this->assertSame(['test' => true], $content->get('data'));

        $lrd = $content->get('_lrd');
        $this->assertCount(5, $lrd);
        $this->assertArrayHasKey('queries', $lrd);
        $this->assertArrayHasKey('logs', $lrd);
        $this->assertArrayHasKey('models', $lrd);
        $this->assertArrayHasKey('modelsTimeline', $lrd);
        $this->assertArrayHasKey('memory', $lrd);
    }

    public function testJsonResponseIsNotObject(): void
    {
        Route::get('middleware', static fn () => response()->json('abc'))->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = new Collection($response->json());

        $this->assertSame('abc', $content->get('data'));

        $lrd = $content->get('_lrd');
        $this->assertCount(5, $lrd);
    }

    public function testResponseIsGzipable(): void
    {
        Route::get('middleware', static fn () => response()->json(['test' => true]))->middleware(LaravelRequestDocsMiddleware::class);

        $this->get(
            'middleware',
            [
                'X-Request-LRD'   => true,
                'Accept-Encoding' => 'gzip',
            ],
        )
            ->assertStatus(200);
    }

    public function testLogListenerIsWorking(): void
    {
        Route::get('middleware', static function () {
            Log::info('aaa');
            return response()->json(['test' => true]);
        })->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = new Collection($response->json());

        $lrd = $content->get('_lrd');

        $this->assertSame([
            [
                'level'   => 'info',
                'message' => 'aaa',
                'context' => [],
            ],
        ], $lrd['logs']);
    }

    public function testDBListenerIsWorking(): void
    {
        Route::get('middleware', static function () {
            DB::select('SELECT 1');
            return response()->json(['test' => true]);
        })->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = new Collection($response->json());

        $lrd = $content->get('_lrd');

        $this->assertNotEmpty($lrd['queries']);
        $this->assertSame('SELECT 1', $lrd['queries'][0]['sql']);
    }
}
