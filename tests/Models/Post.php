<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Test with different route key name.
     */
    public function getRouteKeyName(): string
    {
        return 'name';
    }
}
