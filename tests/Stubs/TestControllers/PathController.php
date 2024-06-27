<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rakutentech\LaravelRequestDocs\Tests\Models\Comment;
use Rakutentech\LaravelRequestDocs\Tests\Models\Post;
use Rakutentech\LaravelRequestDocs\Tests\Models\User;

class PathController
{
    /**
     * Test different variable name with route parameter name.
     */
    public function index(Request $request, int $differentNameIsOkay): Response
    {
        return response('content');
    }

    /**
     * `$name` has no type hint, test generate with default string type.
     */
    // phpcs:ignore
    public function optional(Request $request, $name = null): Response
    {
        return response('content');
    }

    /**
     * Test bind User model.
     */
    public function model(Request $request, User $user, Post $post, Comment $comment): Response
    {
        return response('content');
    }
}
