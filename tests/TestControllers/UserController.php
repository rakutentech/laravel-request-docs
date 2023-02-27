<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestControllers;

use Illuminate\Http\Response;

class UserController
{
    public function __invoke(): Response
    {
        return response('true');
    }
}
