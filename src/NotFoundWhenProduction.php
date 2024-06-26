<?php

namespace Rakutentech\LaravelRequestDocs;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotFoundWhenProduction
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('prod', 'production')) {
            return response()->json([
                'status'      => 'forbidden',
                'status_code' => Response::HTTP_FORBIDDEN,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
