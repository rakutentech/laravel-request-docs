<?php

namespace Rakutentech\LaravelRequestDocs\Tests\TestControllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Group1Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        return response();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        return response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        return response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        return response();
    }
}