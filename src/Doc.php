<?php

namespace Rakutentech\LaravelRequestDocs;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @codeCoverageIgnore
 */
class Doc implements Arrayable
{
    /**
     * The URI pattern the route responds to.
     */
    private string $uri;

    /**
     * The list of HTTP methods the route responds to.
     * Most of the time contains only 1 HTTP method.
     * If a route is defined as `GET`, `HEAD` is always injected into this property.
     *
     * @var string[]
     */
    private array $methods;

    /**
     * The middlewares attached to the route.
     *
     * @var string[]
     */
    private array $middlewares;

    /**
     * The route controller short name.
     * Empty if the route action is a closure.
     */
    private string $controller;

    /**
     * The controller fully qualified name used for the route.
     * Empty if the route action is a closure.
     */
    private string $controllerFullPath;

    /**
     * The (Controller) method name of the route action.
     * Empty if the route action is a closure.
     */
    private string $method;

    /**
     * The HTTP method the route responds to.
     */
    private string $httpMethod;

    /**
     * The parsed validation rules.
     *
     * @var array<string, string[]>
     */
    private array $rules;

    /**
     * The additional description about this route.
     */
    private string $docBlock;

    /**
     * A list of HTTP response codes in string format.
     *
     * @var string[]
     */
    private array $responses;

    /**
     * A list of route path parameters, such as `/users/{id}`.
     *
     * @var array<string, string[]>
     */
    private array $pathParameters;

    /**
     * The group name of the route.
     */
    private string $group;

    /**
     * The group index of the group, determine the ordering.
     */
    private int $groupIndex;

    /**
     * @param  string[]  $methods
     * @param  string[]  $middlewares
     * @param  array<string, string[]>  $pathParameters
     * @param  array<string, string[]>  $rules
     */
    public function __construct(
        string $uri,
        array $methods,
        array $middlewares,
        string $controller,
        string $controllerFullPath,
        string $method,
        string $httpMethod,
        array $pathParameters,
        array $rules,
        string $docBlock
    ) {
        $this->uri                = $uri;
        $this->methods            = $methods;
        $this->middlewares        = $middlewares;
        $this->controller         = $controller;
        $this->controllerFullPath = $controllerFullPath;
        $this->method             = $method;
        $this->httpMethod         = $httpMethod;
        $this->pathParameters     = $pathParameters;
        $this->rules              = $rules;
        $this->docBlock           = $docBlock;
        $this->responses          = [];
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param  string[]  $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @return string[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param  string[]  $middlewares
     */
    public function setMiddlewares(array $middlewares): void
    {
        $this->middlewares = $middlewares;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    public function getControllerFullPath(): string
    {
        return $this->controllerFullPath;
    }

    public function setControllerFullPath(string $controllerFullPath): void
    {
        $this->controllerFullPath = $controllerFullPath;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function setHttpMethod(string $httpMethod): void
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * @return array<string, string[]>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param  array<string, string[]>  $rules
     */
    public function mergeRules(array $rules): void
    {
        $this->rules = array_merge(
            $this->rules,
            $rules,
        );
    }

    /**
     * @param  array<string, string[]>  $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    public function getDocBlock(): string
    {
        return $this->docBlock;
    }

    public function setDocBlock(string $docBlock): void
    {
        $this->docBlock = $docBlock;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    public function getGroupIndex(): int
    {
        return $this->groupIndex;
    }

    public function setGroupIndex(int $groupIndex): void
    {
        $this->groupIndex = $groupIndex;
    }

    public function isClosure(): bool
    {
        return $this->controller === '';
    }

    /**
     * @return string[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param  string[]  $responses
     */
    public function setResponses(array $responses): void
    {
        $this->responses = $responses;
    }

    /**
     * @return array<string, string[]>
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    public function clone(): self
    {
        return clone $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'uri'                  => $this->uri,
            'middlewares'          => $this->middlewares,
            'controller'           => $this->controller,
            'controller_full_path' => $this->controllerFullPath,
            'method'               => $this->method,
            'http_method'          => $this->httpMethod,
            'path_parameters'      => $this->pathParameters,
            'rules'                => $this->rules,
            'doc_block'            => $this->docBlock,
            'responses'            => $this->responses,
        ];

        if (isset($this->group)) {
            $result['group'] = $this->group;
        }

        if (isset($this->groupIndex)) {
            $result['group_index'] = $this->groupIndex;
        }

        return $result;
    }
}
