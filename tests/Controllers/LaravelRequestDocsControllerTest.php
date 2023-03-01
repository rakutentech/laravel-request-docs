<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Rakutentech\LaravelRequestDocs\Tests\TestCase;

class LaravelRequestDocsControllerTest extends TestCase
{
    public function testApi()
    {
        $this->get(route('request-docs.api'))
            ->assertStatus(Response::HTTP_OK);
    }

    public function testAbleFetchAllMethods()
    {
        $response = $this->get(route('request-docs.api'))
            ->assertStatus(Response::HTTP_OK);

        $docs = collect($response->json());

        $this->assertSame([
            Request::METHOD_DELETE,
            Request::METHOD_GET,
            Request::METHOD_HEAD,
            Request::METHOD_PATCH,
            Request::METHOD_POST,
            Request::METHOD_PUT,
        ],
            $docs->pluck('http_method')
                ->flatten()
                ->unique()
                ->sort()
                ->values()
                ->toArray()
        );
    }

    public function testAbleFilterMethod()
    {
        $methodMap = [
            'showDelete' => Request::METHOD_DELETE,
            'showGet'    => Request::METHOD_GET,
            'showHead'   => Request::METHOD_HEAD,
            'showPatch'  => Request::METHOD_PATCH,
            'showPost'   => Request::METHOD_POST,
            'showPut'    => Request::METHOD_PUT,
        ];

        foreach ($methodMap as $request => $method) {
            $response = $this->get(route('request-docs.api') . '?' . $request . '=false')
                ->assertStatus(Response::HTTP_OK);

            $docs = collect($response->json());

            $expected = array_filter([
                Request::METHOD_DELETE,
                Request::METHOD_GET,
                Request::METHOD_HEAD,
                Request::METHOD_PATCH,
                Request::METHOD_POST,
                Request::METHOD_PUT,
            ], fn($expectedMethod) => $expectedMethod !== $method);

            $expected = array_values($expected);

            $this->assertSame(
                $expected,
                $docs->pluck('http_method')
                    ->flatten()
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray()
            );
        }
    }

    public function testOnlyRouteURIStartWith()
    {
        Config::set('request-docs.only_route_uri_start_with', 'welcome');

        $response = $this->get(route('request-docs.api'))
            ->assertStatus(Response::HTTP_OK);

        $docs = collect($response->json());

        foreach ($docs as $doc) {
            $this->assertStringStartsWith('welcome', $doc['uri']);
        }
    }

    public function testOpenApi()
    {
        $response = $this->get(route('request-docs.api') . '?openapi=true')
            ->assertStatus(Response::HTTP_OK);
    }
}
