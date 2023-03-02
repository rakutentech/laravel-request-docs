<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

use Illuminate\Http\Response;

class UserController
{
    public function __invoke(): Response
    {
        return response('true');
    }
}
