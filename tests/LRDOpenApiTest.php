<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

class LRDOpenApiTest extends TestCase
{
    public function testDocsCount()
    {
        $docs = $this->lrd->getDocs();
        $openApi = $this->lrdToOpenApi->openApi($docs)->toArray();

        $this->assertSame($this->countRoutesWithLRDDoc(), count($docs));

        $countRoutes = 0;
        foreach ($openApi["paths"] as $path) {
            $countRoutes += count(array_keys($path));
        }

        $this->assertSame($this->countRoutesWithLRDDoc(), $countRoutes);
    }
}
