<?php

namespace Rakutentech\LaravelRequestDocs;

use Route;
use ReflectionMethod;
use ReflectionClass;
use Illuminate\Support\Str;
use Exception;
use Throwable;

class LaravelRequestDocs
{
    public function getDocs(): array
    {
        $docs = [];
        $excludePatterns = config('request-docs.hide_matching') ?? [];
        $controllersInfo = $this->getControllersInfo();
        $controllersInfo = $this->appendRequestRules($controllersInfo);
        foreach ($controllersInfo as $controllerInfo) {
            try {
                $exclude = false;
                foreach ($excludePatterns as $regex) {
                    $uri = $controllerInfo['uri'];
                    if (preg_match($regex, $uri)) {
                        $exclude = true;
                    }
                }
                if (!$exclude) {
                    $docs[] = $controllerInfo;
                }
            } catch (Exception $exception) {
                continue;
            }
        }
        return array_filter($docs);
    }

    public function sortDocs(array $docs, $sortBy = 'default'): array
    {
        if ($sortBy === 'route_names') {
            sort($docs);
            return $docs;
        }
        $sorted = [];
        $methods = [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
        ];
        foreach ($methods as $method) {
            foreach ($docs as $key => $doc) {
                if (in_array($method, $doc['methods'])) {
                    if (!in_array($doc, $sorted)) {
                        $sorted[] = $doc;
                    }
                }
            }
        }
        return $sorted;
    }

    public function getControllersInfo(): array
    {
        $controllersInfo = [];
        $routes = collect(Route::getRoutes());
        $onlyRouteStartWith = config('request-docs.only_route_uri_start_with') ?? '';

        /** @var \Illuminate\Routing\Route $route */
        foreach ($routes as $route) {
            if ($onlyRouteStartWith && !Str::startsWith($route->uri, $onlyRouteStartWith)) {
                continue;
            }

            try {
                $actionControllerName = $route->action['controller'] ?? $route->action["0"];
                /// Show Only Controller Name
                $controllerFullPath = explode('@', $actionControllerName)[0];
                $getStartWord = strrpos(explode('@', $actionControllerName)[0], '\\') + 1;
                $controllerName = substr($controllerFullPath, $getStartWord);

                $method = explode('@', $actionControllerName)[1] ?? '__invoke';
                $httpMethod = $route->methods[0];

                foreach ($controllersInfo as $controllerInfo) {
                    if ($controllerInfo['uri'] == $route->uri && $controllerInfo['httpMethod'] == $httpMethod) {
                        // is duplicate
                        continue 2;
                    }
                }

                $middlewares = [];
                if (!empty($route->action['middleware'])) {
                    $middlewares = !is_array($route->action['middleware']) ? [$route->action['middleware']] : $route->action['middleware'];
                }

                $controllersInfo[] = [
                    'uri'                   => $route->uri,
                    'methods'               => $route->methods,
                    'middlewares'           => $middlewares,
                    'controller'            => $controllerName,
                    'controller_full_path'  => $controllerFullPath,
                    'method'                => $method,
                    'httpMethod'            => $httpMethod,
                    'rules'                 => [],
                    'docBlock'              => ""
                ];
            } catch (Exception $e) {
                continue;
            }
        }

        return $controllersInfo;
    }

    public function appendRequestRules(array $controllersInfo): array
    {
        foreach ($controllersInfo as $index => $controllerInfo) {
            $controller       = $controllerInfo['controller_full_path'];
            $method           = $controllerInfo['method'];
            try {
                $reflectionMethod = new ReflectionMethod($controller, $method);
            } catch (Throwable $e) {
                // Skip to next if controller is not exists.
                if (config('request-docs.debug')) {
                    throw $e; // @codeCoverageIgnore
                }
                continue;
            }
            $params           = $reflectionMethod->getParameters();
            $customRules = $this->customParamsDocComment($reflectionMethod->getDocComment());
            $controllersInfo[$index]['rules'] = [];

            foreach ($params as $param) {
                if (!$param->getType()) {
                    continue;
                }
                $requestClassName = $param->getType()->getName();
                $requestClass = null;
                try {
                    $reflection = new ReflectionClass($requestClassName);
                    $requestClass = $reflection->newInstanceWithoutConstructor();
                } catch (Throwable $th) {
                    //throw $th;
                }

                foreach (config('request-docs.request_methods') as $requestMethod) {
                    if ($requestClass && method_exists($requestClass, $requestMethod)) {
                        try {
                            $controllersInfo[$index]['rules'] = array_merge($controllersInfo[$index]['rules'], $this->flattenRules($requestClass->$requestMethod()));
                        } catch (Throwable $e) {
                            $controllersInfo[$index]['rules'] = array_merge($controllersInfo[$index]['rules'], $this->rulesByRegex($requestClassName, $requestMethod));
                            if (config('request-docs.debug')) {
                                throw $e;
                            }
                        }
                    }
                }

                $controllersInfo[$index]['rules'] = array_merge(
                    $controllersInfo[$index]['rules'] ?? [],
                    $customRules,
                );
            }
            $controllersInfo[$index]['docBlock'] = $this->lrdDocComment($reflectionMethod->getDocComment());
        }
        return $controllersInfo;
    }

    public function lrdDocComment($docComment): string
    {
        $lrdComment = "";
        $counter = 0;
        foreach (explode("\n", $docComment) as $comment) {
            $comment = trim($comment);
            // check contains in string
            if (Str::contains($comment, '@lrd')) {
                $counter++;
            }
            if ($counter == 1 && !Str::contains($comment, '@lrd')) {
                if (Str::startsWith($comment, '*')) {
                    $comment = trim(substr($comment, 1));
                }
                // remove first character from string
                $lrdComment .= $comment . "\n";
            }
        }
        return $lrdComment;
    }

    public function flattenRules($mixedRules)
    {
        $rules = [];
        foreach ($mixedRules as $attribute => $rule) {
            if (is_object($rule)) {
                $rule = get_class($rule);
                $rules[$attribute][] = $rule;
            } elseif (is_array($rule)) {
                $rulesStrs = [];
                foreach ($rule as $ruleItem) {
                    $rulesStrs[] = is_object($ruleItem) ? get_class($ruleItem) : $ruleItem;
                }
                $rules[$attribute][] = implode("|", $rulesStrs);
            } else {
                $rules[$attribute][] = $rule;
            }
        }

        return $rules;
    }

    public function rulesByRegex($requestClassName, $methodName)
    {
        $data = new ReflectionMethod($requestClassName, $methodName);
        $lines = file($data->getFileName());
        $rules = [];

        for ($i = $data->getStartLine() - 1; $i <= $data->getEndLine() - 1; $i++) {
            // check if => in string, only pick up rules that are coded on single line
            if (Str::contains($lines[$i], '=>')) {
                preg_match_all("/(?:'|\").*?(?:'|\")/", $lines[$i], $matches);
                $rules[] =  $matches;
            }
        }

        $rules = collect($rules)
            ->filter(function ($item) {
                return count($item[0]) > 0;
            })
            ->transform(function ($item) {
                $fieldName = Str::of($item[0][0])->replace(['"', "'"], '');
                $definedFieldRules = collect(array_slice($item[0], 1))->transform(function ($rule) {
                    return Str::of($rule)->replace(['"', "'"], '')->__toString();
                })->toArray();

                return ['key' => $fieldName, 'rules' => $definedFieldRules];
            })
            ->keyBy('key')
            ->transform(function ($item) {
                return $item['rules'];
            })->toArray();

        return $rules;
    }

    private function customParamsDocComment($docComment): array
    {
        $params = [];

        foreach (explode("\n", $docComment) as $comment) {
            if (Str::contains($comment, '@QAparam')) {
                $comment = trim(Str::replace(['@QAparam', '*'], '', $comment));

                $comment = explode(' ', $comment);

                if (count($comment) > 0) {
                    $params[$comment[0]] = array_values(array_filter($comment, fn($item) => $item != $comment[0]));
                }
            }
        }

        return $params;
    }
}
