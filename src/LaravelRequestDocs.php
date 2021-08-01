<?php

namespace Rakutentech\LaravelRequestDocs;

use Route;
use ReflectionMethod;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
class LaravelRequestDocs
{

    public function getDocs()
    {
        $docs = [];
        $excludePatterns = config('request-docs.hide_matching') ?? [];
        $controllersInfo = $this->getControllersInfo();
        $controllersInfo = $this->appendRequestRules($controllersInfo);
        foreach ($controllersInfo as $controllerInfo) {
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
        }
        return array_filter($docs);
    }

    public function getControllersInfo(): Array
    {
        $controllersInfo = [];
        $routes = collect(Route::getRoutes());
        foreach ($routes as $route) {
            $controllersInfo[] = [
                'uri'        => $route->uri,
                'methods'    => $route->methods,
                'middleware' => $route->action['middleware'],
                'controller' => explode('@', $route->action['controller'])[0],
                'method'     => explode('@', $route->action['controller'])[1],
            ];
        }

        return $controllersInfo;
    }

    public function appendRequestRules(Array $controllersInfo)
    {
        foreach ($controllersInfo as $index => $controllerInfo) {
            $controllersInfo[$index]['rules']  = [];
            $controller       = $controllerInfo['controller'];
            $method           = $controllerInfo['method'];
            $reflectionMethod = new ReflectionMethod($controller, $method);
            $params           = $reflectionMethod->getParameters();

            foreach ($params as $param) {
                if (!$param->getType()) {
                    continue;
                }
                $requestClassName = $param->getType()->getName();
                try {
                    $requestClass = new $requestClassName();
                    if ($requestClass instanceof FormRequest) {
                        $controllersInfo[$index]['rules'] = $this->flattenRules($requestClass->rules());
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }
        return $controllersInfo;
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
}
