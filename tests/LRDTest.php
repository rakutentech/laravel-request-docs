<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Support\Facades\Config;

class LRDTest extends TestCase
{
    public function testGetDocs()
    {
        $docs = $this->lrd->getDocs();

        $this->assertSame($this->countRoutesWithLRDDoc(), count($docs));

        $docSize = 9;
        $firstDoc = $docs[0];
        $this->assertCount($docSize, $firstDoc);
        $this->assertArrayHasKey('uri', $firstDoc);
        $this->assertArrayHasKey('methods', $firstDoc);
        $this->assertArrayHasKey('middlewares', $firstDoc);
        $this->assertArrayHasKey('controller', $firstDoc);
        $this->assertArrayHasKey('controller_full_path', $firstDoc);
        $this->assertArrayHasKey('method', $firstDoc);
        $this->assertArrayHasKey('httpMethod', $firstDoc);
        $this->assertArrayHasKey('rules', $firstDoc);
        $this->assertArrayHasKey('docBlock', $firstDoc);
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

    public function testOnlyRouteURIStartWith()
    {
        Config::set('request-docs.only_route_uri_start_with', 'welcome');
        $docs = $this->lrd->getDocs();
        foreach ($docs as $doc) {
            $this->assertStringStartsWith('welcome', $doc['uri']);
        }
    }
}
