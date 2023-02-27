<?php

namespace Rakutentech\LaravelRequestDocs;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class LaravelRequestDocs
{
    /**
     * @return \Rakutentech\LaravelRequestDocs\Doc[]
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function getDocs(): array
    {
        $docs            = [];
        $excludePatterns = config('request-docs.hide_matching') ?? [];
        $controllersInfo = $this->getControllersInfo();
        $controllersInfo = $this->appendRequestRules($controllersInfo);
        foreach ($controllersInfo as $controllerInfo) {
            try {
                $exclude = false;
                foreach ($excludePatterns as $regex) {
                    if (preg_match($regex, $controllerInfo->getUri())) {
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

    /**
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $docs
     * @param  string|null  $sortBy
     * @return \Rakutentech\LaravelRequestDocs\Doc[]
     */
    public function sortDocs(array $docs, ?string $sortBy = 'default'): array
    {
        if ($sortBy === null || $sortBy === 'default') {
            return $docs;
        }

        if ($sortBy === 'route_names') {
            sort($docs);
            return $docs;
        }

        /** @var \Rakutentech\LaravelRequestDocs\Doc[] $sorted */
        $sorted  = [];

        $methods = [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'HEAD',
        ];

        foreach ($methods as $method) {
            foreach ($docs as $doc) {
                if (in_array($method, $doc->getMethods())) {
                    if (!in_array($doc, $sorted)) {
                        // Overwrite methods without mutation.
                        $clone = clone $doc;
                        $clone->setMethods([$method]);
                        $sorted[] = $clone;
                    }
                }
            }
        }
        return $sorted;
    }

    /**
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $docs
     * @return \Rakutentech\LaravelRequestDocs\Doc[]
     */
    public function filterByMethods(array $docs, bool $get, bool $post, bool $put, bool $patch, bool $delete, bool $head): array
    {
        $filtered = [];

        foreach ($docs as $doc) {
            if ($get && in_array('GET', $doc->getMethods())) {
                $clone = clone $doc;
                $clone->setMethods(['GET']);
                $filtered[] = $clone;
            }
        }

        foreach ($docs as $doc) {
            if ($post && in_array('POST', $doc->getMethods())) {
                $clone = clone $doc;
                $clone->setMethods(['POST']);
                $filtered[] = $clone;
            }
        }

        foreach ($docs as $doc) {
            if ($put && in_array('PUT', $doc->getMethods())) {
                $clone = clone $doc;
                $clone->setMethods(['PUT']);
                $filtered[] = $clone;
            }
        }

        foreach ($docs as $doc) {
            if ($patch && in_array('PATCH', $doc->getMethods())) {
                $clone = clone $doc;
                $clone->setMethods(['PATCH']);
                $filtered[] = $clone;
            }
        }

        foreach ($docs as $doc) {
            if ($delete && in_array('DELETE', $doc->getMethods())) {
                $clone = clone $doc;
                $clone->setMethods(['DELETE']);
                $filtered[] = $clone;
            }
        }

        foreach ($docs as $doc) {
            if ($head && in_array('HEAD', $doc->getMethods())) {
                $clone = clone $doc;
                $clone->setMethods(['HEAD']);
                $filtered[] = $clone;
            }
        }

        return $filtered;
    }

    /**
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $docs
     * @return \Rakutentech\LaravelRequestDocs\Doc[]
     */
    public function groupDocs(array $docs, ?string $group = 'default'): array
    {
        if ($group === null || $group === 'default') {
            return $docs;
        }

        if ($group === 'api_uri') {
            $this->groupDocsByAPIURI($docs);
        }

        if ($group === 'controller_full_path') {
            $this->groupDocsByFQController($docs);
        }

        return collect($docs)
            ->sortBy(function (Doc $doc) {
                return $doc->getGroup() . $doc->getGroupIndex();
            }, SORT_NATURAL)
            ->values()
            ->all();
    }

    /**
     * @return \Rakutentech\LaravelRequestDocs\Doc[]
     * @throws \ReflectionException
     */
    public function getControllersInfo(): array
    {
        /** @var \Rakutentech\LaravelRequestDocs\Doc[] $controllersInfo */
        $controllersInfo = [];
        $routes          = Route::getRoutes()->getRoutes();

        $onlyRouteStartWith = config('request-docs.only_route_uri_start_with') ?? '';

        foreach ($routes as $route) {
            if ($onlyRouteStartWith && !Str::startsWith($route->uri, $onlyRouteStartWith)) {
                continue;
            }

            $controllerName     = '';
            $controllerFullPath = '';
            $method             = '';

            // `$route->action['uses']` value is either 'Class@method' string or Closure.
            if (is_string($route->action['uses'])) {
                $controllerCallback = Str::parseCallback($route->action['uses']);
                $controllerFullPath = $controllerCallback[0];
                $method             = $controllerCallback[1];
                $controllerName     = (new ReflectionClass($controllerFullPath))->getShortName();
            }

            $httpMethod = $route->methods[0];

            foreach ($controllersInfo as $controllerInfo) {
                if ($controllerInfo->getUri() === $route->uri && $controllerInfo->getHttpMethod() == $httpMethod) {
                    // is duplicate
                    continue 2;
                }
            }

            $doc = new Doc(
                $route->uri,
                $route->methods,
                config('request-docs.hide_meta_data') ? [] : $route->middleware(),
                config('request-docs.hide_meta_data') ? '' : $controllerName,
                config('request-docs.hide_meta_data') ? '' : $controllerFullPath,
                config('request-docs.hide_meta_data') ? '' : $method,
                $httpMethod,
                [],
                '',
            );

            $controllersInfo[] = $doc;
        }

        return $controllersInfo;
    }

    /**
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $controllersInfo
     * @return \Rakutentech\LaravelRequestDocs\Doc[]
     * @throws \ReflectionException
     */
    public function appendRequestRules(array $controllersInfo): array
    {
        foreach ($controllersInfo as $controllerInfo) {
            if ($controllerInfo->isClosure()) {
                // Skip to next if controller is not exists.
                continue;
            }

            $reflectionMethod = new ReflectionMethod($controllerInfo->getControllerFullPath(), $controllerInfo->getMethod());

            $docComment = $this->getDocComment($reflectionMethod);

            $customRules = $this->customParamsDocComment($docComment);
            $controllerInfo->setResponses($this->customResponsesDocComment($docComment));

            foreach ($reflectionMethod->getParameters() as $param) {
                if (!$param->getType()) {
                    continue;
                }

                try {
                    $requestClassName = $param->getType()->getName();
                    $reflectionClass  = new ReflectionClass($requestClassName);
                    $requestObject    = $reflectionClass->newInstanceWithoutConstructor();

                    foreach (config('request-docs.rules_methods') as $requestMethod) {
                        if (!method_exists($requestObject, $requestMethod)) {
                            continue;
                        }

                        try {
                            $controllerInfo->mergeRules($this->flattenRules($requestObject->$requestMethod()));
                        } catch (Throwable $e) {
                            $controllerInfo->mergeRules($this->rulesByRegex($requestClassName, $requestMethod));

                            if (config('request-docs.debug')) {
                                throw $e;
                            }
                        }
                    }
                } catch (Throwable $th) {
                    // Do nothing.
                }

                $controllerInfo->mergeRules($customRules);
            }

            $controllerInfo->setDocBlock($this->lrdDocComment($docComment));
        }
        return $controllersInfo;
    }

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

    /**
     * @param  array<string, \Illuminate\Contracts\Validation\Rule|array|string>  $mixedRules
     * @return array<string, string[]>
     */
    public function flattenRules(array $mixedRules): array
    {
        /** @var array<string, string[]> $rules */
        $rules = [];

        foreach ($mixedRules as $attribute => $rule) {
            if (is_object($rule)) {
                $rules[$attribute][] = get_class($rule);
                continue;
            }

            if (is_array($rule)) {
                /** @var string[] $rulesStrs */
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
     * @return array<string, string[]>
     * @throws \ReflectionException
     */
    public function rulesByRegex(string $requestClassName, string $methodName): array
    {
        $data  = new ReflectionMethod($requestClassName, $methodName);
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
                $rules[] = $matches;
            }
        }

        $rules = collect($rules)
            ->filter(function ($item) {
                return count($item[0]) > 0;
            })
            ->transform(function ($item) {
                $fieldName         = Str::of($item[0][0])->replace(['"', "'"], '');
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

    /**
     * @param  string  $docComment
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

            if (count($comments) > 0) {
                $params[$comments[0]] = array_values(array_filter($comments, fn($item) => $item !== $comments[0]));
            }
        }

        return $params;
    }

    /**
     * @param  string  $docComment
     * @return string[]
     */
    private function customResponsesDocComment(string $docComment): array
    {
        /** @var string[] $params */
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
     * @param  string[]  $delimiters
     * @return string[]
     */
    private function multiExplode(array $delimiters, string $string): array
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        return explode($delimiters[0], $ready);
    }

    /**
     * Parse the `$docs['uri']` and attach `group` and `group_index` details.
     *
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $docs
     */
    private function groupDocsByAPIURI(array $docs): void
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
            $this->setGroupInfo($doc, $group, $groupIndexes->get($group));
        }
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
     *
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $docs
     */
    private function groupDocsByFQController(array $docs): void
    {
        // To remember group indexes with group => index pair.
        $groupIndexes = collect();

        foreach ($docs as $doc) {
            $group = $doc->getControllerFullPath();
            $this->rememberGroupIndex($groupIndexes, $group);
            $this->setGroupInfo($doc, $group, $groupIndexes->get($group));
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

    /**
     * @param  \ReflectionMethod  $reflectionMethod
     * @return string
     */
    private function getDocComment(ReflectionMethod $reflectionMethod): string
    {
        $docComment = $reflectionMethod->getDocComment();

        if ($docComment === false) {
            return '';
        }

        return $docComment;
    }
}
