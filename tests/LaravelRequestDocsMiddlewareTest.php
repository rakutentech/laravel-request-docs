<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsMiddleware;

class LaravelRequestDocsMiddlewareTest extends TestCase
{
    public function testMissingLRDHeader()
    {
        Route::get('middleware', function () {
            return ['test' => true];
        })->middleware(LaravelRequestDocsMiddleware::class);

        $this->get('middleware')
            ->assertStatus(200)
            ->assertExactJson(['test' => true]);
    }

    public function testNotJsonResponse()
    {
        Route::get('middleware', function () {
            return 1;
        })->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200)
            ->assertExactJson([1]);
    }

    public function testJsonResponseIsObject()
    {
        Route::get('middleware', function () {
            return response()->json(['test' => true]);
        })->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = collect($response->json());

        $this->assertSame(['test' => true], $content->get('data'));

        $lrd = $content->get('_lrd');
        $this->assertCount(5, $lrd);
        $this->assertArrayHasKey('queries', $lrd);
        $this->assertArrayHasKey('logs', $lrd);
        $this->assertArrayHasKey('models', $lrd);
        $this->assertArrayHasKey('modelsTimeline', $lrd);
        $this->assertArrayHasKey('memory', $lrd);
    }

    public function testJsonResponseIsNotObject()
    {
        Route::get('middleware', function () {
            return response()->json('abc');
        })->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = collect($response->json());

        $this->assertSame('abc', $content->get('data'));

        $lrd = $content->get('_lrd');
        $this->assertCount(5, $lrd);
    }

    public function testResponseIsGzipable()
    {
        Route::get('middleware', function () {
            return response()->json(['test' => true]);
        })->middleware(LaravelRequestDocsMiddleware::class);

        $this->get(
            'middleware',
            [
                'X-Request-LRD'   => true,
                'Accept-Encoding' => 'gzip',
            ]
        )
            ->assertStatus(200);
    }

    public function testLogListenerIsWorking()
    {
        Route::get('middleware', function () {
            Log::info('aaa');
            return response()->json(['test' => true]);
        })->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = collect($response->json());

        $lrd = $content->get('_lrd');

        $this->assertSame([
            [
                'level'   => 'info',
                'message' => 'aaa',
                'context' => [],
            ],
        ], $lrd['logs']);
    }

    public function testDBListenerIsWorking()
    {
        Route::get('middleware', function () {
            DB::select('SELECT 1');
            return response()->json(['test' => true]);
        })->middleware(LaravelRequestDocsMiddleware::class);

        $response = $this->get('middleware', ['X-Request-LRD' => true])
            ->assertStatus(200);

        $content = collect($response->json());

        $lrd = $content->get('_lrd');

        $this->assertNotEmpty($lrd['queries']);
        $this->assertSame('SELECT 1', $lrd['queries'][0]['sql']);
    }
}
