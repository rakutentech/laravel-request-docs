<?php

namespace Rakutentech\LaravelRequestDocs;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use KitLoong\AppLogger\QueryLog\LogWriter as QueryLogger;

class LaravelRequestDocsMiddleware extends QueryLogger
{
    private array $queries = [];

    public function handle($request, Closure $next)
    {
        if (!$request->headers->has('X-Request-LRD') || !config('app.debug')) {
            return $next($request);
        }

        $this->listenDB();
        $response = $next($request);

        $content = $response->getData();
        $content->_lrd = [
            'queries' => $this->queries,
            'memory' => (string) round(memory_get_peak_usage(true) / 1048576, 2) . "MB",
        ];
        $jsonContent = json_encode($content);

        if (in_array('gzip', $request->getEncodings()) && function_exists('gzencode')) {
            $level = 9; // best compression;
            $jsonContent = gzencode($jsonContent, $level);
            $response->headers->add([
                'Content-type' => 'application/json; charset=utf-8',
                'Content-Length'=> strlen($jsonContent),
                'Content-Encoding' => 'gzip',
            ]);
        }
        $response->setContent($jsonContent);
        return $response;
    }

    public function listenDB()
    {
        DB::listen(function (QueryExecuted $query) {
            $this->queries[] = $this->getMessages($query);
        });
    }
}
