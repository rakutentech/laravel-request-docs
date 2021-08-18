<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Closure;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

class LaravelRequestDocsMiddleware extends Middleware
{
    private array $queries = [];

    public function handle($request, Closure $next, ...$guards)
    {
        if (!$request->headers->has('X-Request-LRD') || !config('app.debug')) {
            return $next($request);
        }

        $this->listenDB();
        $response = $next($request);

        $content = json_decode($response->content(), true);
        $content['_lrd'] = [
            'queries' => $this->queries
        ];

        $json = new JsonResource($content);

        return $json;
    }

    public function listenDB()
    {
        DB::listen(function (QueryExecuted $query) {
            $this->queries[] = $this->getMessages($query);
        });
    }

    protected function getMessages(QueryExecuted $query): array
    {
        $sql = $query->sql;

        foreach ($query->bindings as $key => $binding) {
            // https://github.com/barryvdh/laravel-debugbar/blob/master/src/DataCollector/QueryCollector.php#L138
            // This regex matches placeholders only, not the question marks,
            // nested in quotes, while we iterate through the bindings
            // and substitute placeholders by suitable values.
            $regex = is_numeric($key)
                ? "/(?<!\?)\?(?=(?:[^'\\\']*'[^'\\']*')*[^'\\\']*$)(?!\?)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            // Mimic bindValue and only string data types
            if (is_string($binding)) {
                $binding = $this->quote($binding);
            }

            $sql = preg_replace($regex, $binding, $sql, 1);
        }

        return [
            'time' => $query->time,
            'sql' => $sql,
        ];
    }

    /**
     * Mimic mysql_real_escape_string
     *
     * @param  string  $value
     * @return string
     */
    protected function quote(string $value): string
    {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'];

        return "'".str_replace($search, $replace, $value)."'";
    }
}
