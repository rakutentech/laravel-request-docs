<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use Rakutentech\LaravelRequestDocs\Tests\TestCase;

class LaravelRequestDocsCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(config('request-docs.docs_path'));
        File::deleteDirectory(base_path('docs/request-docs/'));

        parent::tearDown();
    }

    public function testHandle()
    {
        $this->assertFalse(File::exists(config('request-docs.docs_path') . '/index.html'));
        $this->assertFalse(File::exists(config('request-docs.docs_path') . '/lrd-openapi.json'));

        $this->artisan('lrd:generate')
            ->assertExitCode(0);

        $this->assertTrue(File::exists(config('request-docs.docs_path') . '/index.html'));
        $this->assertTrue(File::exists(config('request-docs.docs_path') . '/lrd-openapi.json'));

        config('request-docs.docs_path');
    }

    public function testWillCreateDirectory()
    {
        File::deleteDirectory(config('request-docs.docs_path'));
        $this->assertFalse(File::exists(config('request-docs.docs_path')));

        $this->artisan('lrd:generate')
            ->assertExitCode(0);

        $this->assertTrue(File::exists(config('request-docs.docs_path')));
    }
}
