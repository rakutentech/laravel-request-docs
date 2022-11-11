<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Feature\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Rakutentech\LaravelRequestDocs\Tests\TestCase;

class LaravelRequestDocsControllerTest extends TestCase
{
    public function testIndex()
    {
        $this->get(config('request-docs.url'))
            ->assertStatus(Response::HTTP_OK);
    }

    public function testSortDocsByDefault()
    {
        Config::set('request-docs.sort_by', 'default');
        $this->get(config('request-docs.url'))
            ->assertStatus(Response::HTTP_OK);
    }

    public function testOpenAPI()
    {
        $this->get(config('request-docs.url') . '?openapi=true')
            ->assertStatus(Response::HTTP_OK);
    }
}
