<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Support\Collection;
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
        if ($sortBy === 'default') {
            return $docs;
        }
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
            'HEAD',
        ];
        foreach ($methods as $method) {
            foreach ($docs as $key => $doc) {
                if (in_array($method, $doc['methods'])) {
                    if (!in_array($doc, $sorted)) {
                        $doc['methods'] = [$method];
                        $sorted[] = $doc;
                    }
                }
            }
        }
        return $sorted;
    }

    public function filterByMethods($docs, $get, $post, $put, $path, $delete, $head)
    {
        $filtered = [];
        foreach ($docs as $key => $doc) {
            if ($get && in_array('GET', $doc['methods'])) {
                $_doc = $doc;
                $_doc['methods'] = ['GET'];
                $filtered[] = $_doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if ($post && in_array('POST', $doc['methods'])) {
                $_doc = $doc;
                $_doc['methods'] = ['POST'];
                $filtered[] = $_doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if ($put && in_array('PUT', $doc['methods'])) {
                $_doc = $doc;
                $_doc['methods'] = ['PUT'];
                $filtered[] = $_doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if ($path && in_array('PATCH', $doc['methods'])) {
                $_doc = $doc;
                $_doc['methods'] = ['PATCH'];
                $filtered[] = $_doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if ($delete && in_array('DELETE', $doc['methods'])) {
                $_doc = $doc;
                $_doc['methods'] = ['DELETE'];
                $filtered[] = $_doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if ($head && in_array('HEAD', $doc['methods'])) {
                $_doc = $doc;
                $_doc['methods'] = ['HEAD'];
                $filtered[] = $_doc;
            }
        }

        return $filtered;
    }

    public function groupDocs($docs, $group = 'default')
    {
        if ($group === 'default') {
            return $docs;
        }

        $groupDocs = [];

        if ($group === 'api_uri') {
            $groupDocs = $this->groupDocsByAPIURI($docs);
        }

        if ($group === 'controller_full_path') {
            $groupDocs = $this->groupDocsByFQController($docs);
        }

        return collect($groupDocs)->sortBy(['group', 'group_index'])
            ->values()
            ->toArray();
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
                    'middlewares'           => config('request-docs.hide_meta_data') ? [] : $middlewares,
                    'controller'            => config('request-docs.hide_meta_data') ? '' : $controllerName,
                    'controller_full_path'  => config('request-docs.hide_meta_data') ? '' : $controllerFullPath,
                    'method'                => config('request-docs.hide_meta_data') ? '' : $method,
                    'httpMethod'            => $httpMethod,
                    'rules'                 => [],
                    'docBlock'              => "",
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
            $docComment       = $reflectionMethod->getDocComment();
            $customRules      = $this->customParamsDocComment($docComment);
            $customResponses  = $this->customResponsesDocComment($docComment);
            $controllersInfo[$index]['responses'] = $customResponses;
            $controllersInfo[$index]['rules'] = [];

            foreach ($params as $param) {
                if (!$param->getType()) {
                    continue;
                }
                if (class_exists(ReflectionUnionType::class) && $paramType instanceof ReflectionUnionType) {
                    $requestClassName = $param->getName();
                } else {
                    $requestClassName = $param->getType()->getName();
                }

                $requestClass = null;
                try {
                    $reflection = new ReflectionClass($requestClassName);
                    try {
                        $requestClass = $reflection->newInstance();
                    } catch (Throwable $th) {
                        $requestClass = $reflection->newInstanceWithoutConstructor();
                    }
                } catch (Throwable $th) {
                    //throw $th;
                }

                foreach (config('request-docs.rules_methods') as $requestMethod) {
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
            // check if line is a comment
            $trimmed = trim($lines[$i]);
            if (Str::startsWith($trimmed, '//') || Str::startsWith($trimmed, '#')) {
                continue;
            }
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
            if (Str::contains($comment, '@LRDparam')) {
                $comment = trim(Str::replace(['@LRDparam', '*'], '', $comment));

                $comment = $this->multiexplode([' ', '|'], $comment);

                if (count($comment) > 0) {
                    $params[$comment[0]] = array_values(array_filter($comment, fn($item) => $item != $comment[0]));
                }
            }
        }
        return $params;
    }
    private function customResponsesDocComment($docComment): array
    {
        $params = [];

        foreach (explode("\n", $docComment) as $comment) {
            if (Str::contains($comment, '@LRDresponses')) {
                $comment = trim(Str::replace(['@LRDresponses', '*'], '', $comment));

                $comment = $this->multiexplode([' ', '|'], $comment);

                $params = $comment;
            }
        }
        if (count($params) == 0) {
            $params = config('request-docs.default_responses') ?? [];
        }
        return $params;
    }

    private function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }

    /**
     * Parse the `$docs['uri']` and attach `group` and `group_index` details.
     *
     * @param  array  $docs
     * @return array  $docs
     */
    private function groupDocsByAPIURI(array $docs): array
    {
        $patterns = config('request-docs.group_by.uri_patterns', []);

        $regex = count($patterns) > 0 ? '(' . implode('|', $patterns) . ')' : '';

        // A collection<string, int> to remember indexes with `group` => `index` pair.
        $groupIndexes = collect();

        foreach ($docs as $i => $doc) {
            if ($regex !== '') {
                // If $regex    = '^api/v[\d]+/',
                // and $uri     = '/api/v1/users',
                // then $prefix = '/api/v1/'.
                $prefix = Str::match($regex, $doc['uri']);
            }

            $group = $this->getGroupByURI($prefix ?? '', $doc['uri']);
            $groupIndexes = $this->rememberGroupIndex($groupIndexes, $group);
            $docs[$i] = $this->attachGroupInfo($doc, $group, $groupIndexes->get($group));
        }

        return $docs;
    }

    /**
     * Create and return group name by the `$uri`.
     */
    private function getGroupByURI(string $prefix, string $uri): string
    {
        if ($prefix === '') {
            // No prefix, create group by the first path.
            $paths = explode('/', $uri);
            return $paths[0];
        }

        // Glue the prefix + "first path after prefix" to form a group.
        $after = (Str::after($uri, $prefix));
        $paths = explode('/', $after);
        return $prefix . $paths[0];
    }

    /**
     * Parse the `$docs['controller_full_path']` and attach `group` and `group_index` details.
     */
    private function groupDocsByFQController(array $docs): array
    {
        // To remember group indexes with group => index pair.
        $groupIndexes = collect();

        foreach ($docs as $i => $doc) {
            $group = $doc['controller_full_path'];
            $groupIndexes = $this->rememberGroupIndex($groupIndexes, $group);
            $docs[$i] = $this->attachGroupInfo($doc, $group, $groupIndexes->get($group));
        }
        return $docs;
    }

    /**
     * Set the last index number into `$groupIndexes`
     *
     * @param  \Illuminate\Support\Collection<string, int>  $groupIndexes  [`group` => `index`]
     */
    private function rememberGroupIndex(Collection $groupIndexes, string $key): Collection
    {
        if (!$groupIndexes->has($key)) {
            $groupIndexes->put($key, 0);
            return $groupIndexes;
        }

        $groupIndexes->put($key, $groupIndexes->get($key) + 1);
        return $groupIndexes;
    }

    /**
     * Attach `group` and `group_index` into `$doc`.
     */
    private function attachGroupInfo(array $doc, string $group, int $groupIndex): array
    {
        $doc['group'] = $group;
        $doc['group_index'] = $groupIndex;
        return $doc;
    }
}
