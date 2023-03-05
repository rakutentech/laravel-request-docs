<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

use Illuminate\Http\Response;
use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\RequestWithEmptyRules;

class MatchController
{
    /**
     * Display a listing of the resource.
     */
    public function index(RequestWithEmptyRules $request): Response
    {
        return response('content');
    }
}
