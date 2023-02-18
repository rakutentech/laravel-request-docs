<?php

namespace Rakutentech\LaravelRequestDocs;

use Closure;
use Illuminate\Http\Response;

class NotFoundWhenProduction
{
    public function handle($request, Closure $next)
    {
        if (app()->environment('prod')) {
            return response()->json([
                'status' => 'forbidden',
                'status_code' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
