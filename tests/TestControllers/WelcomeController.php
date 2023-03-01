<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestControllers;

use Rakutentech\LaravelRequestDocs\Tests\TestRequests\RequestWithoutRules;
use Rakutentech\LaravelRequestDocs\Tests\TestRequests\WelcomeIndexRequest;
use Rakutentech\LaravelRequestDocs\Tests\TestRequests\WelcomeEditRequest;
use Rakutentech\LaravelRequestDocs\Tests\TestRequests\WelcomeStoreRequest;
use Rakutentech\LaravelRequestDocs\Tests\TestRequests\WelcomeDeleteRequest;

class WelcomeController
{
    /**
     * @lrd:start
     * #Hello markdown
     * ## Documentation for /my route
     * @lrd:end
     */
    public function index(WelcomeIndexRequest $request)
    {
        return 1;
    }

    public function show()
    {
        return 1;
    }

    /**
     * @LRDparam search_string string
     * @LRDparam search_array array
     * @LRDparam search_integer integer
     * @LRDparam search_boolean boolean
     */
    public function edit(WelcomeEditRequest $request)
    {
        return 1;
    }

    public function store(int $id, WelcomeStoreRequest $request)
    {
        return 1;
    }

    public function destroy(WelcomeDeleteRequest $request)
    {
        return 1;
    }

    /**
     * Test request without `rules` method
     */
    public function noRules(RequestWithoutRules $request)
    {
        return 1;
    }

    public function health($unknown)
    {
        return 1;
    }
}
