<?php

namespace Rakutentech\LaravelRequestDocs;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use KitLoong\AppLogger\QueryLog\LogWriter as QueryLogger;
use Log;

class LaravelRequestDocsMiddleware extends QueryLogger
{
    private array $queries = [];
    private array $logs = [];

    public function handle($request, Closure $next)
    {
        if (!$request->headers->has('X-Request-LRD') || !config('app.debug')) {
            return $next($request);
        }

        $this->listenDB();
        $this->listenToLogs();
        $response = $next($request);

        try {
            $response->getData();
        } catch (\Exception $e) {
            // not a json response
            return $response;
        }

        $content = $response->getData();
        $content->_lrd = [
            'queries' => $this->queries,
            'logs' => $this->logs,
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
    public function listenToLogs()
    {
        Log::listen(function ($message) {
            $this->logs[] = $message;
        });
    }
}
