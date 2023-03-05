<?php

namespace Rakutentech\LaravelRequestDocs;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use KitLoong\AppLogger\QueryLog\LogWriter as QueryLogger;
use Symfony\Component\HttpFoundation\Response;

class LaravelRequestDocsMiddleware extends QueryLogger
{
    private array $queries        = [];
    private array $logs           = [];
    private array $models         = [];
    private array $modelsTimeline = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('request-docs.enabled')) {
            return $next($request);
        }

        if (!config('app.debug') && $request->headers->has('X-Request-LRD')) {
            $response    = $next($request);
            $jsonContent = json_encode([
                // because php stan is not what it used to be
                /** @phpstan-ignore-next-line */
                'data' => $response->getData()
            ]);
            $response->setContent($jsonContent);
            return $response;
        }

        if (!config('app.debug')) {
            return $next($request);
        }
        if (!$request->headers->has('X-Request-LRD')) {
            return $next($request);
        }


        if (!config('request-docs.hide_sql_data')) {
            $this->listenToDB();
        }
        if (!config('request-docs.hide_logs_data')) {
            $this->listenToLogs();
        }
        if (!config('request-docs.hide_models_data')) {
            $this->listenToModels();
        }

        $response = $next($request);

        if (!$response instanceof JsonResponse) {
            return $response;
        }

        $content = [
            'data' => $response->getData(),
            '_lrd' => [
                'queries'        => $this->queries,
                'logs'           => $this->logs,
                'models'         => $this->models,
                // 'modelsTimeline' => $this->modelsTimeline,
                'modelsTimeline' => array_unique($this->modelsTimeline, SORT_REGULAR),
                'memory'         => ((string) round(memory_get_peak_usage(true) / 1048576, 2)) . "MB",
            ],
        ];

        $jsonContent = json_encode($content);

        if (in_array('gzip', $request->getEncodings()) && function_exists('gzencode')) {
            $level       = 9; // best compression;
            $jsonContent = gzencode($jsonContent, $level);
            $response->headers->add([
                'Content-type'     => 'application/json; charset=utf-8',
                'Content-Length'   => strlen($jsonContent),
                'Content-Encoding' => 'gzip',
            ]);
        }
        $response->setContent($jsonContent);
        return $response;
    }

    public function listenToDB(): void
    {
        DB::listen(function (QueryExecuted $query) {
            $this->queries[] = $this->getMessages($query);
        });
    }

    public function listenToLogs(): void
    {
        Log::listen(function ($message) {
            $this->logs[] = $message;
        });
    }

    public function listenToModels(): void
    {
        Event::listen('eloquent.*', function ($event, $models) {
            foreach (array_filter($models) as $model) {
                // doing and booted ignore
                if (Str::startsWith($event, 'eloquent.booting')
                    || Str::startsWith($event, 'eloquent.booted')
                    || Str::startsWith($event, 'eloquent.retrieving')
                    || Str::startsWith($event, 'eloquent.creating')
                    || Str::startsWith($event, 'eloquent.saving')
                    || Str::startsWith($event, 'eloquent.updating')
                    || Str::startsWith($event, 'eloquent.deleting')
                ) {
                    continue;
                }
                // split $event by : and take first part
                $event = explode(':', $event)[0];
                $event = Str::replace('eloquent.', '', $event);
                $class = get_class($model);

                $this->modelsTimeline[] = [
                    'event' => $event,
                    'model' => $class,
                ];

                if (!isset($this->models[$class])) {
                    $this->models[$class] = [];
                }
                if (!isset($this->models[$class][$event])) {
                    $this->models[$class][$event] = 0;
                }
                $this->models[$class][$event] = $this->models[$class][$event] + 1;
            }
        });
    }
}
