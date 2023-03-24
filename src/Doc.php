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
     *
     * @var string
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
     *
     * @var string
     */
    private string $controller;

    /**
     * The controller fully qualified name used for the route.
     * Empty if the route action is a closure.
     *
     * @var string
     */
    private string $controllerFullPath;

    /**
     * The (Controller) method name of the route action.
     * Empty if the route action is a closure.
     *
     * @var string
     */
    private string $method;

    /**
     * The HTTP method the route responds to.
     *
     * @var string
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
     *
     * @var string
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
     * @var array<string, string>
     */
    private array $pathParameters;

    /**
     * The group name of the route.
     *
     * @var string
     */
    private string $group;

    /**
     * The group index of the group, determine the ordering.
     *
     * @var int
     */
    private int $groupIndex;

    /**
     * @param  string  $uri
     * @param  string[]  $methods
     * @param  string[]  $middlewares
     * @param  string  $controller
     * @param  string  $controllerFullPath
     * @param  string  $method
     * @param  string  $httpMethod
     * @param  array<string, string[]>  $rules
     * @param  string  $docBlock
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

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param  string  $uri
     */
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

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param  string  $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getControllerFullPath(): string
    {
        return $this->controllerFullPath;
    }

    /**
     * @param  string  $controllerFullPath
     */
    public function setControllerFullPath(string $controllerFullPath): void
    {
        $this->controllerFullPath = $controllerFullPath;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param  string  $method
     */
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
     * @return array
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param  array  $responses
     */
    public function setResponses(array $responses): void
    {
        $this->responses = $responses;
    }

    /**
     * @return array<string, string>
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    public function clone(): Doc
    {
        return clone $this;
    }

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
