<?php

namespace Rakutentech\LaravelRequestDocs;

use Route;
use ReflectionMethod;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Exception;

class LaravelRequestDocs
{

    public function getDocs()
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
        foreach ($docs as $key => $doc) {
            if (in_array('GET', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('POST', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('PUT', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('PATCH', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('DELETE', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        return $sorted;
    }

    public function getControllersInfo(): array
    {
        $controllersInfo = [];
        $routes = collect(Route::getRoutes());
        foreach ($routes as $route) {
            try {
                /// Show Pnly Controller Name
                $controllerFullPath = explode('@', $route->action['controller'])[0];
                $getStartWord = strrpos(explode('@', $route->action['controller'])[0], '\\') + 1;
                $controllerName = substr($controllerFullPath, $getStartWord);

                /// Has Auth Token
                $hasAuthToken = !is_array($route->action['middleware']) ? [$route->action['middleware']] : $route->action['middleware'];

                $controllersInfo[] = [
                    'uri'                   => $route->uri,
                    'methods'               => $route->methods,
                    'middlewares'           => !is_array($route->action['middleware']) ? [$route->action['middleware']] : $route->action['middleware'],
                    'controller'            => $controllerName,
                    'controller_full_path'  => $controllerFullPath,
                    'method'                => explode('@', $route->action['controller'])[1],
                    'rules'                 => [],
                    'docBlock'              => "",
                    'bearer'                => in_array('auth:api', $hasAuthToken)
                ];
            } catch (Exception $e) {
                continue;
            }
        }

        return $controllersInfo;
    }

    public function appendRequestRules(array $controllersInfo)
    {
        foreach ($controllersInfo as $index => $controllerInfo) {
            $controller       = $controllerInfo['controller_full_path'];
            $method           = $controllerInfo['method'];
            $reflectionMethod = new ReflectionMethod($controller, $method);
            $params           = $reflectionMethod->getParameters();

            foreach ($params as $param) {
                if (!$param->getType()) {
                    continue;
                }
                $requestClassName = $param->getType()->getName();
                $requestClass = null;
                try {
                    $requestClass = new $requestClassName();
                } catch (\Throwable $th) {
                    //throw $th;
                }
                if ($requestClass instanceof FormRequest) {
                    try {
                        $controllersInfo[$index]['rules'] = $this->flattenRules($requestClass->rules());
                    } catch (\ErrorException $th) {
                        $controllerInfo[$index]['rules'] = $this->rulesByRegex($requestClassName);
                    }
                    $controllersInfo[$index]['docBlock'] = $this->lrdDocComment($reflectionMethod->getDocComment());
                }
            }
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

    // get text between first and last tag
    private function getTextBetweenTags($docComment, $tag1, $tag2)
    {
        $docComment = trim($docComment);
        $start = strpos($docComment, $tag1);
        $end = strpos($docComment, $tag2);
        $text = substr($docComment, $start + strlen($tag1), $end - $start - strlen($tag1));
        return $text;
    }

    public function flattenRules($mixedRules)
    {
        $rules = [];
        foreach ($mixedRules as $attribute => $rule) {
            if (is_object($rule)) {
                $rule = get_class($rule);
                $rules[$attribute][] = $rule;
            } else if (is_array($rule)) {
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

    public function rulesByRegex($requestClassName)
    {
        $data = new ReflectionMethod($requestClassName, 'rules');
        $lines = file($data->getFileName());
        $rules = [];
        for ($i = $data->getStartLine() - 1; $i <= $data->getEndLine() - 1; $i++) {
            preg_match_all("/(?:'|\").*?(?:'|\")/", $lines[$i], $matches);
            $rules[] =  $matches;
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
}
