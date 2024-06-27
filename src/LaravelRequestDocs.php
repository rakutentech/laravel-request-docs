<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class LaravelRequestDocs
{
    private RoutePath $routePath;

    public function __construct(RoutePath $routePath)
    {
        $this->routePath = $routePath;
    }

    /**
     * Get a collection of {@see \Rakutentech\LaravelRequestDocs\Doc} with route and rules information.
     *
     * @return \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>
     * @throws \ReflectionException
     */
    public function getDocs(
        bool $showGet,
        bool $showPost,
        bool $showPut,
        bool $showPatch,
        bool $showDelete,
        bool $showHead
    ): Collection {
        $filteredMethods = array_filter([
            Request::METHOD_GET    => $showGet,
            Request::METHOD_POST   => $showPost,
            Request::METHOD_PUT    => $showPut,
            Request::METHOD_PATCH  => $showPatch,
            Request::METHOD_DELETE => $showDelete,
            Request::METHOD_HEAD   => $showHead,
        ], static fn (bool $shouldShow) => $shouldShow);

        $methods = array_keys($filteredMethods);

        $docs = $this->getControllersInfo($methods);
        $docs = $this->appendRequestRules($docs);

        return $docs->filter();
    }

    /**
     * Loop and split {@see \Rakutentech\LaravelRequestDocs\Doc} by {@see \Rakutentech\LaravelRequestDocs\Doc::$methods}.
     *
     * @param  \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>  $docs
     * @return \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>
     */
    public function splitByMethods(Collection $docs): Collection
    {
        $splitDocs = collect();

        foreach ($docs as $doc) {
            foreach ($doc->getMethods() as $method) {
                $cloned = $doc->clone();
                $cloned->setMethods([$method]);
                $cloned->setHttpMethod($method);
                $splitDocs->push($cloned);
            }
        }

        return $splitDocs;
    }

    /**
     * Sort by `$sortBy`.
     *
     * @param  \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>  $docs
     * @return \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>
     */
    public function sortDocs(Collection $docs, ?string $sortBy = 'default'): Collection
    {
        if (!in_array($sortBy, ['route_names', 'method_names'])) {
            return $docs;
        }

        if ($sortBy === 'route_names') {
            return $docs->sort()->values();
        }

        // Sort by `method_names`.
        $methods = [
            Request::METHOD_GET,
            Request::METHOD_POST,
            Request::METHOD_PUT,
            Request::METHOD_PATCH,
            Request::METHOD_DELETE,
            Request::METHOD_HEAD,
        ];

        $sorted = $docs->sortBy(static fn (Doc $doc) => array_search($doc->getHttpMethod(), $methods), SORT_NUMERIC);

        return $sorted->values();
    }

    /**
     * Group by `$groupBy`. {@see \Rakutentech\LaravelRequestDocs\Doc::$group} and {@see \Rakutentech\LaravelRequestDocs\Doc::$groupIndex} will be set.
     * The return collection is always sorted by `group`, `group_index`.
     *
     * @param  \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>  $docs
     * @return \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>
     */
    public function groupDocs(Collection $docs, ?string $groupBy = 'default'): Collection
    {
        if (!in_array($groupBy, ['api_uri', 'controller_full_path'])) {
            return $docs;
        }

        if ($groupBy === 'api_uri') {
            $this->groupDocsByAPIURI($docs);
        }

        if ($groupBy === 'controller_full_path') {
            $this->groupDocsByFQController($docs);
        }

        return $docs
            ->sortBy(static fn (Doc $doc) => $doc->getGroup() . $doc->getGroupIndex(), SORT_NATURAL)
            ->values();
    }

    /**
     * Get controllers and routes information and return a list of {@see \Rakutentech\LaravelRequestDocs\Doc}
     *
     * @param  string[]  $onlyMethods
     * @return \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>
     * @throws \ReflectionException
     */
    // TODO Should reduce complexity
    // phpcs:ignore
    public function getControllersInfo(array $onlyMethods): Collection
    {
        $docs = collect();

        $routes = Route::getRoutes()->getRoutes();

        $onlyRouteStartWith = config('request-docs.only_route_uri_start_with') ?? '';
        $excludePatterns    = config('request-docs.hide_matching') ?? [];

        foreach ($routes as $route) {
            if ($onlyRouteStartWith && !Str::startsWith($route->uri, $onlyRouteStartWith)) {
                continue;
            }

            foreach ($excludePatterns as $regex) {
                if (preg_match($regex, $route->uri)) {
                    continue 2;
                }
            }

            $routeMethods = array_intersect($route->methods, $onlyMethods);

            if (count($routeMethods) === 0) {
                continue;
            }

            $controllerName     = '';
            $controllerFullPath = '';
            $method             = '';

            // `$route->action['uses']` value is either 'Class@method' string or Closure.
            if (is_string($route->action['uses']) && !RouteAction::containsSerializedClosure($route->action)) {
                /** @var array{0: class-string<\Illuminate\Routing\Controller>, 1: string} $controllerCallback */
                $controllerCallback = Str::parseCallback($route->action['uses']);
                $controllerFullPath = $controllerCallback[0];
                $method             = $controllerCallback[1];
                $controllerName     = (new ReflectionClass($controllerFullPath))->getShortName();
            }

            $pathParameters = [];
            $pp             = $this->routePath->getPathParameters($route);

            // same format as rules
            foreach ($pp as $k => $v) {
                $pathParameters[$k] = [$v];
            }

            /** @var string[] $middlewares */
            $middlewares = $route->middleware();

            $doc = new Doc(
                $route->uri,
                $routeMethods,
                config('request-docs.hide_meta_data') ? [] : $middlewares,
                config('request-docs.hide_meta_data') ? '' : $controllerName,
                config('request-docs.hide_meta_data') ? '' : $controllerFullPath,
                config('request-docs.hide_meta_data') ? '' : $method,
                '',
                $pathParameters,
                [],
                '',
            );

            $docs->push($doc);
        }

        return $docs;
    }

    /**
     * Parse from request object and set into {@see \Rakutentech\LaravelRequestDocs\Doc}
     * This method also read docBlock and update into {@see \Rakutentech\LaravelRequestDocs\Doc}.
     *
     * @param  \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>  $docs
     * @return \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>
     * @throws \ReflectionException
     */
    // TODO Should reduce complexity
    // phpcs:ignore
    public function appendRequestRules(Collection $docs): Collection
    {
        foreach ($docs as $doc) {
            if ($doc->isClosure()) {
                // Skip to next if controller is not exists.
                continue;
            }

            $controllerReflectionMethod = new ReflectionMethod($doc->getControllerFullPath(), $doc->getMethod());

            $controllerMethodDocComment = $this->getDocComment($controllerReflectionMethod);

            $controllerMethodLrdComment = $this->lrdDocComment($controllerMethodDocComment);
            $controllerMethodDocRules   = $this->customParamsDocComment($controllerMethodDocComment);

            $doc->setResponses($this->customResponsesDocComment($controllerMethodDocComment));

            $lrdDocComments = [];

            foreach ($controllerReflectionMethod->getParameters() as $param) {
                $namedType = $param->getType();

                if (!$namedType) {
                    continue;
                }

                try {
                    if (!method_exists($namedType, 'getName')) {
                        continue;
                    }

                    $requestClassName = $namedType->getName();

                    if (!class_exists($requestClassName)) {
                        continue;
                    }

                    $reflectionClass = new ReflectionClass($requestClassName);

                    try {
                        $requestObject = $reflectionClass->newInstance();
                    } catch (Throwable $ex) {
                        $requestObject = $reflectionClass->newInstanceWithoutConstructor();
                    }

                    foreach (config('request-docs.rules_methods') as $requestMethod) {
                        if (!method_exists($requestObject, $requestMethod)) {
                            continue;
                        }

                        try {
                            $doc->mergeRules($this->flattenRules($requestObject->$requestMethod()));
                            $requestReflectionMethod = new ReflectionMethod($requestObject, $requestMethod);
                        } catch (Throwable $ex) {
                            $doc->mergeRules($this->rulesByRegex($requestClassName, $requestMethod));
                            $requestReflectionMethod = new ReflectionMethod($requestClassName, $requestMethod);
                        }

                        $requestMethodDocComment = $this->getDocComment($requestReflectionMethod);

                        $requestMethodLrdComment = $this->lrdDocComment($requestMethodDocComment);
                        $requestMethodDocRules   = $this->customParamsDocComment($requestMethodDocComment);

                        $lrdDocComments[] = $requestMethodLrdComment;
                        $doc->mergeRules($requestMethodDocRules);
                    }
                } catch (Throwable $ex) {
                    // Do nothing.
                }
            }

            $lrdDocComments[] = $controllerMethodLrdComment;
            $lrdDocComments   = array_filter($lrdDocComments, static fn ($s) => $s !== '');
            $doc->setDocBlock(join("\n", $lrdDocComments));
            $doc->mergeRules($controllerMethodDocRules);
        }

        return $docs;
    }

    /**
     * Get description in between @lrd:start and @lrd:end from the doc block.
     */
    public function lrdDocComment(string $docComment): string
    {
        $lrdComment = "";
        $counter    = 0;

        foreach (explode("\n", $docComment) as $comment) {
            $comment = trim($comment);

            // check contains in string
            if (Str::contains($comment, '@lrd')) {
                $counter++;
            }

            if ($counter !== 1 || Str::contains($comment, '@lrd')) {
                continue;
            }

            if (Str::startsWith($comment, '*')) {
                $comment = substr($comment, 1);
            }

            // remove first character from string
            $lrdComment .= $comment . "\n";
        }

        return $lrdComment;
    }

    /**
     * Parse rules from the request.
     *
     * @param  array<string, \Illuminate\Contracts\Validation\Rule|array<\Illuminate\Contracts\Validation\Rule|string>|string>  $mixedRules
     * @return array<string, string[]>  Key is attribute, value is a list of rules.
     */
    public function flattenRules(array $mixedRules): array
    {
        $rules = [];

        foreach ($mixedRules as $attribute => $rule) {
            if (is_object($rule)) {
                $rules[$attribute][] = get_class($rule);
                continue;
            }

            if (is_array($rule)) {
                $rulesStrs = [];

                foreach ($rule as $ruleItem) {
                    $rulesStrs[] = is_object($ruleItem) ? get_class($ruleItem) : $ruleItem;
                }

                $rules[$attribute][] = implode("|", $rulesStrs);
                continue;
            }

            $rules[$attribute][] = $rule;
        }

        return $rules;
    }

    /**
     * Read the source file and parse rules by regex.
     *
     * @return array<string, string[]> Key is attribute, value is a list of rules.
     * @throws \ReflectionException
     */
    public function rulesByRegex(string $requestClassName, string $methodName): array
    {
        $data  = new ReflectionMethod($requestClassName, $methodName);
        $lines = file((string) $data->getFileName());

        if ($lines === false) {
            return [];
        }

        $rules = [];

        for ($i = $data->getStartLine() - 1; $i <= $data->getEndLine() - 1; $i++) {
            // check if line is a comment
            $trimmed = trim($lines[$i]);

            if (Str::startsWith($trimmed, '//') || Str::startsWith($trimmed, '#')) {
                continue; // @codeCoverageIgnore
            }

            // check if => in string, only pick up rules that are coded on single line
            if (!Str::contains($lines[$i], '=>')) {
                continue;
            }

            preg_match_all("/(?:'|\").*?(?:'|\")/", $lines[$i], $matches);
            $rules[] = $matches;
        }

        return collect($rules)
            ->filter(static fn ($item) => count($item[0]) > 0)
            ->map(static function (array $item) {
                $fieldName         = Str::of($item[0][0])->replace(['"', "'"], '');
                $definedFieldRules = collect(array_slice($item[0], 1))->transform(static fn ($rule) => Str::of($rule)->replace(['"', "'"], '')->__toString())->toArray();

                return ['key' => $fieldName, 'rules' => $definedFieldRules];
            })
            ->keyBy('key')
            ->map(static fn ($item) => $item['rules'])
            ->toArray();
    }

    /**
     * Get additional rules by parsing the doc block.
     *
     * @return array<string, string[]>
     */
    private function customParamsDocComment(string $docComment): array
    {
        $params = [];

        foreach (explode("\n", $docComment) as $comment) {
            if (!Str::contains($comment, '@LRDparam')) {
                continue;
            }

            $comment = trim(Str::replace(['@LRDparam', '*'], '', $comment));

            $comments = $this->multiExplode([' ', '|'], $comment);

            if (count($comments) <= 0) {
                continue;
            }

            $params[$comments[0]] = array_values(array_filter($comments, static fn ($item) => $item !== $comments[0]));
        }

        return $params;
    }

    /**
     * Get responses by parsing the doc block.
     *
     * @return string[]  A list of responses. Will overwrite the default responses.
     */
    private function customResponsesDocComment(string $docComment): array
    {
        $params = [];

        foreach (explode("\n", $docComment) as $comment) {
            if (!Str::contains($comment, '@LRDresponses')) {
                continue;
            }

            $comment = trim(Str::replace(['@LRDresponses', '*'], '', $comment));

            $params = $this->multiExplode([' ', '|'], $comment);
        }

        if (count($params) === 0) {
            return config('request-docs.default_responses') ?? [];
        }

        return $params;
    }

    /**
     * @param  array<non-empty-string>  $delimiters
     * @return string[]
     */
    private function multiExplode(array $delimiters, string $string): array
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        return explode($delimiters[0], $ready);
    }

    /**
     * Group by {@see \Rakutentech\LaravelRequestDocs\Doc::$uri} and attach {@see \Rakutentech\LaravelRequestDocs\Doc::$group} and {@see \Rakutentech\LaravelRequestDocs\Doc::$groupIndex} details.
     *
     * @param  \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>  $docs
     */
    private function groupDocsByAPIURI(Collection $docs): void
    {
        $patterns = config('request-docs.group_by.uri_patterns', []);

        $regex = count($patterns) > 0 ? '(' . implode('|', $patterns) . ')' : '';

        // A collection<string, int> to remember indexes with `group` => `index` pair.
        $groupIndexes = collect();

        foreach ($docs as $doc) {
            if ($regex !== '') {
                // If $regex    = '^api/v[\d]+/',
                // and $uri     = '/api/v1/users',
                // then $prefix = '/api/v1/'.
                $prefix = Str::match($regex, $doc->getUri());
            }

            $group = $this->getGroupByURI($prefix ?? '', $doc->getUri());
            $this->rememberGroupIndex($groupIndexes, $group);
            $this->setGroupInfo($doc, $group, (int) $groupIndexes->get($group));
        }
    }

    /**
     * Create and return group name by the {@see \Rakutentech\LaravelRequestDocs\Doc::$uri}.
     */
    private function getGroupByURI(string $prefix, string $uri): string
    {
        if ($prefix === '') {
            // No prefix, create group by the first path.
            $paths = explode('/', $uri);
            return $paths[0];
        }

        // Glue the prefix + "first path after prefix" to form a group.
        $after = Str::after($uri, $prefix);
        $paths = explode('/', $after);
        return $prefix . $paths[0];
    }

    /**
     * Group by {@see \Rakutentech\LaravelRequestDocs\Doc::$controllerFullPath} and attach {@see \Rakutentech\LaravelRequestDocs\Doc::$group} and {@see \Rakutentech\LaravelRequestDocs\Doc::$groupIndex} details.
     *
     * @param  \Illuminate\Support\Collection<int, \Rakutentech\LaravelRequestDocs\Doc>  $docs
     */
    private function groupDocsByFQController(Collection $docs): void
    {
        // To remember group indexes with group => index pair.
        $groupIndexes = collect();

        foreach ($docs as $doc) {
            $group = $doc->getControllerFullPath();
            $this->rememberGroupIndex($groupIndexes, $group);
            $this->setGroupInfo($doc, $group, (int) $groupIndexes->get($group));
        }
    }

    /**
     * Set the last index number into `$groupIndexes`
     *
     * @param  \Illuminate\Support\Collection<string, int>  $groupIndexes  [`group` => `index`]
     */
    private function rememberGroupIndex(Collection $groupIndexes, string $key): void
    {
        if (!$groupIndexes->has($key)) {
            $groupIndexes->put($key, 0);
            return;
        }

        $groupIndexes->put($key, $groupIndexes->get($key) + 1);
    }

    /**
     * Attach `group` and `group_index` into `$doc`.
     */
    private function setGroupInfo(Doc $doc, string $group, int $groupIndex): void
    {
        $doc->setGroup($group);
        $doc->setGroupIndex($groupIndex);
    }

    private function getDocComment(ReflectionMethod $reflectionMethod): string
    {
        $docComment = $reflectionMethod->getDocComment();

        if ($docComment === false) {
            return '';
        }

        return $docComment;
    }
}
