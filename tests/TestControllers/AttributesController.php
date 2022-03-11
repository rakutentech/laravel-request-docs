<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestControllers;

use Rakutentech\LaravelRequestDocs\Attributes\RouteDeprecated;
use Rakutentech\LaravelRequestDocs\Attributes\RouteDescription;

class AttributesController
{

    #[RouteDescription('Title', 'Description')]
    public function first()
    {
        return 1;
    }

    #[RouteDeprecated()]
    public function deprecated()
    {
        return 1;
    }

    #[RouteDeprecated('With comment')]
    public function alsoDeprecated()
    {
        return 1;
    }

}
