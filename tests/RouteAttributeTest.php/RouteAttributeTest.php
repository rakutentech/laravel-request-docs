<?php

namespace Rakutentech\LaravelRequestDocs\Tests\RouteAttributeTest;

use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocs;
use Rakutentech\LaravelRequestDocs\Tests\TestCase;
use Rakutentech\LaravelRequestDocs\Tests\TestControllers\AttributesController;

/**
 * @requires PHP >= 8.0
 */
class RouteAttributeTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        Route::get('lrd/tests/attributes', [AttributesController::class, 'first']);
        Route::get('lrd/tests/attributes/deprecated', [AttributesController::class, 'deprecated']);
        Route::get('lrd/tests/attributes/deprecated/with-comment', [AttributesController::class, 'alsoDeprecated']);
    }

    /** @test */
    public function it_can_build_comment_using_attribute()
    {
        $info = $this->getControllerInfoByUri('lrd/tests/attributes');
        $this->assertEquals("# Title\nDescription", $info['docBlock']);
    }

    /** @test */
    public function it_can_build_deprecated_comment_using_attribute()
    {
        $info = $this->getControllerInfoByUri('lrd/tests/attributes/deprecated');
        $this->assertEquals("**Deprecated!**", $info['docBlock']);

        $info = $this->getControllerInfoByUri('lrd/tests/attributes/deprecated/with-comment');
        $this->assertEquals("**Deprecated!** With comment", $info['docBlock']);
    }

}
