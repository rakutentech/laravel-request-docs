<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

use Illuminate\Http\Response;
use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\UserStoreRequest;

/**
 * class UserController
 * @LRDtag User
 */
class UserController
{
    public function __invoke(): Response
    {
        return response('true');
    }

    /**
     * Store a newly created resource in storage.
     * This method creates a user when validations are met.
     * @param UserStoreRequest $request
     * @return Response
     */
    public function store(UserStoreRequest $request): Response
    {
        return response('true');
    }
}
