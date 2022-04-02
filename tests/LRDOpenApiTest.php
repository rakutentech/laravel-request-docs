<?php

namespace Rakutentech\LaravelRequestDocs\Tests;
use Route;

class LRDOpenApiTest extends TestCase
{
    public function testDocsCount()
    {
        $docs = $this->lrd->getDocs();
        $openApi = $this->lrdToOpenApi->openApi($docs)->toArray();
        $routes = collect(Route::getRoutes());

        $this->assertSame($routes->count(), count($docs));

        $countRoutes = 0;
        foreach ($openApi["paths"] as $path) {
            $countRoutes += count(array_keys($path));
        }

        $this->assertSame($routes->count(), $countRoutes);
    }
}
