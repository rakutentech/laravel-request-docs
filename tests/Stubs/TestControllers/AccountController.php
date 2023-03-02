<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response('content');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        return response('content');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        return response('content');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        return response('content');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        return response('content');
    }
}
