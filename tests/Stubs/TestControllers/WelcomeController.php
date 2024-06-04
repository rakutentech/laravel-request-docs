<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\RequestWithoutRules;
use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\WelcomeDeleteRequest;
use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\WelcomeEditRequest;
use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\WelcomeIndexRequest;
use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\WelcomeStoreRequest;

/**
 * @LRDtag Welcome
 */
class WelcomeController
{
    /**
     * Before
     * @lrd:start
     * #Hello markdown
     * ## Documentation for /my route
     * @lrd:end
     * After
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
     * @LRDresponses 200|400|401
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
