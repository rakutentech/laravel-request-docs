<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Reflector;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class RoutePath
{
    private const TYPE_MAP = [
        'bool' => 'boolean',
        'int'  => 'integer',
    ];

    /**
     * @return array<string, string>
     * @throws \ReflectionException
     */
    public function getPathParameters(Route $route): array
    {
        $pathParameters = $this->initAllParametersWithStringType($route);

        $pathParameters = $this->setParameterType($route, $pathParameters);

        $pathParameters = $this->setOptional($route, $pathParameters);

        $pathParameters = $this->mutateKeyNameWithBindingField($route, $pathParameters);

        return $this->setRegex($route, $pathParameters);
    }

    /**
     * Set route path parameter type.
     * This method will overwrite `$pathParameters` type with the real types found from route declaration.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  array<string, string>  $pathParameters
     * @return array<string, string>
     * @throws \ReflectionException
     */
    private function setParameterType(Route $route, array $pathParameters): array
    {
        $bindableParameters = $this->getBindableParameters($route);

        foreach ($route->parameterNames() as $position => $parameterName) {
            // Check `$bindableParameters` existence by comparing the position of route parameters.
            if (!isset($bindableParameters[$position])) {
                continue;
            }

            $bindableParameter = $bindableParameters[$position];

            // For builtin type, always get the type from reflection parameter.
            if ($bindableParameter['class'] === null) {
                $pathParameters[$parameterName] = $this->getParameterType($bindableParameter['parameter']);
                continue;
            }

            $resolved = $bindableParameter['class'];

            // Check if is model parameter?
            if (!$resolved->isSubclassOf(Model::class)) {
                continue;
            }

            // Model and path parameter name must be the same.
            if ($bindableParameter['parameter']->getName() !== $parameterName) {
                continue;
            }

            $model = $resolved->newInstance();

            // Check if model binding using another column.
            // Skip if user defined column except than default key.
            // Since we do not have the binding column type information, we set to string type.
            $bindingField = $route->bindingFieldFor($parameterName);
            if ($bindingField !== null && $bindingField !== $model->getKeyName()) {
                continue;
            }

            // Try set type from model key type.
            if ($model->getKeyName() === $model->getRouteKeyName()) {
                $pathParameters[$parameterName] = self::TYPE_MAP[$model->getKeyType()] ?? $model->getKeyType();
            }
        }
        return $pathParameters;
    }

    private function getOptionalParameterNames(string $uri): array
    {
        preg_match_all('/\{(\w+?)\?\}/', $uri, $matches);

        return $matches[1] ?? [];
    }

    /**
     * Get bindable parameters in ordered position that are listed in the route / controller signature.
     * This method will filter {@see \Illuminate\Http\Request}.
     * The ordering of returned parameter should be maintained to match with route path parameter.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array<int, array{parameter: \ReflectionParameter, class: \ReflectionClass|null}>
     * @throws \ReflectionException
     */
    private function getBindableParameters(Route $route): array
    {
        /** @var array<int, array{parameter: \ReflectionParameter, class: \ReflectionClass|null}> $parameters */
        $parameters = [];

        foreach ($route->signatureParameters() as $reflectionParameter) {
            $className = Reflector::getParameterClassName($reflectionParameter);

            // Is native type.
            if ($className === null) {
                $parameters[] = [
                    'parameter' => $reflectionParameter,
                    'class'     => null,
                ];
                continue;
            }

            // Check if the class name is a bindable objects, such as model. Skip if not.
            $reflectionClass = new ReflectionClass($className);
            if (!$reflectionClass->implementsInterface(UrlRoutable::class)) {
                continue;
            }

            $parameters[] = [
                'parameter' => $reflectionParameter,
                'class'     => $reflectionClass,
            ];
        }
        return $parameters;
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @param  array<string, string>  $pathParameters
     * @return array<string, string>
     */
    private function setOptional(Route $route, array $pathParameters): array
    {
        $optionalParameters = $this->getOptionalParameterNames($route->uri);

        foreach ($pathParameters as $parameter => $rule) {
            if (in_array($parameter, $optionalParameters)) {
                $pathParameters[$parameter] .= '|nullable';
                continue;
            }

            $pathParameters[$parameter] .= '|required';
        }
        return $pathParameters;
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @param  array<string, string>  $pathParameters
     * @return array<string, string>
     */
    private function setRegex(Route $route, array $pathParameters): array
    {
        foreach ($pathParameters as $parameter => $rule) {
            if (!isset($route->wheres[$parameter])) {
                continue;
            }
            $pathParameters[$parameter] .= '|regex:/' . $route->wheres[$parameter] . '/';
        }

        return $pathParameters;
    }

    /**
     * Set and return route path parameters, with default string type.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array<string, string>
     */
    private function initAllParametersWithStringType(Route $route): array
    {
        return array_fill_keys($route->parameterNames(), 'string');
    }

    /**
     * Get type from method reflection parameter.
     * Return string if type is not declared.
     *
     * @param  \ReflectionParameter  $methodParameter
     * @return string
     */
    private function getParameterType(ReflectionParameter $methodParameter): string
    {
        $reflectionNamedType = $methodParameter->getType();

        if ($reflectionNamedType === null) {
            return 'string';
        }

        // See https://github.com/phpstan/phpstan/issues/3886
        if (!$reflectionNamedType instanceof ReflectionNamedType) {
            return 'string';
        }

        return self::TYPE_MAP[$reflectionNamedType->getName()] ?? $reflectionNamedType->getName();
    }

    /**
     * @param  \Illuminate\Routing\Route  $route
     * @param  array<string, string>  $pathParameters
     * @return array<string, string>
     */
    private function mutateKeyNameWithBindingField(Route $route, array $pathParameters): array
    {
        $mutatedPath = [];

        foreach ($route->parameterNames() as $name) {
            $bindingName = $route->bindingFieldFor($name);

            if ($bindingName === null) {
                $mutatedPath[$name] = $pathParameters[$name];
                continue;
            }

            $mutatedPath["$name:$bindingName"] = $pathParameters[$name];
        }

        return $mutatedPath;
    }
}
