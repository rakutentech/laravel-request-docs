<?php

namespace Rakutentech\LaravelRequestDocs\Tests;
use Route;

class LRDTest extends TestCase
{
    public function testDocsCount()
    {
        $docs = $this->lrd->getDocs();
        $routes = collect(Route::getRoutes());

        $this->assertSame($routes->count(), count($docs));
    }

    public function testDocsCanFetchAllMethods()
    {
        $docs = $this->lrd->getDocs();
        $methods = [];
        foreach ($docs as $doc) {
            $methods = array_merge($methods, $doc['methods']);
        }
        $methods = array_unique($methods);
        sort($methods);
        $this->assertSame(['DELETE', 'GET', 'HEAD', 'POST', 'PUT'], $methods);
    }

    public function testDocsCanFetchInfo()
    {
        $docs = $this->lrd->getDocs();
        foreach ($docs as $doc) {
            $this->assertNotEmpty($doc['rules']);
            $this->assertNotEmpty($doc['methods']);
            // $this->assertNotEmpty($doc['middlewares']); //todo: add middlewares to test
            $this->assertNotEmpty($doc['controller']);
            $this->assertNotEmpty($doc['controller_full_path']);
            $this->assertNotEmpty($doc['method']);
            $this->assertNotEmpty($doc['httpMethod']);
            $this->assertNotEmpty($doc['rules']);
        }
    }
}
