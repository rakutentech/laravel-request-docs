<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Stubs\TestControllers;

use Rakutentech\LaravelRequestDocs\Tests\Stubs\TestRequests\CommentsOnRequestRulesMethodRequest;

class CommentsOnRequestRulesMethodController
{
    /**
     * Before
     *
     * @lrd:start
     * # Controller
     * ## Index Method Comment
     * @lrd:end
     * @LRDparam extra_index_field_1 string|max:32
     * // either space or pipe
     * @LRDparam extra_index_field_2 string|nullable|max:32
     * // duplicate param in controller
     * @LRDparam this_is_a_duplicate_param controller description
     * // override the default response codes
     * @LRDresponses 201|244
     *
     * After
     */
    public function index(CommentsOnRequestRulesMethodRequest $request): int
    {
        return 1;
    }
}
