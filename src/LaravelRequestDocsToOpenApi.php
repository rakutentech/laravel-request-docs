<?php

namespace Rakutentech\LaravelRequestDocs;

/**
 * @phpstan-type OpenApi array{
 *     openapi: string,
 *     info: array{
 *         version: string,
 *         title: string,
 *         description: string,
 *         license: array{
 *             name: string,
 *             url: string,
 *         },
 *     },
 *     servers: array{
 *         url: string,
 *     }
 *  }
 *
 * @phpstan-type Parameter array{
 *      name: string,
 *      description: string,
 *      in: string,
 *      style: string,
 *      required: bool,
 *      schema: array{
 *          type: string
 *      }
 *  }
 *
 * @phpstan-type Property array{type: string, nullable: bool, format: string}
 *
 * @phpstan-type RequestBody array{
 *     description: string,
 *     content: array<string, array{
 *             schema: array{
 *                 type: string,
 *                 properties: array<string, Property>
 *             }
 *         }
 *     >
 * }
 */
class LaravelRequestDocsToOpenApi
{
    /**
     * @var OpenApi
     */
    private array $openApi;

    /**
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $docs
     * @return $this
     */
    public function openApi(array $docs): self
    {
        $this->openApi['openapi']                 = config('request-docs.open_api.version', '3.0.0');
        $this->openApi['info']['version']         = config('request-docs.open_api.document_version', '1.0.0');
        $this->openApi['info']['title']           = config('request-docs.open_api.title', 'Laravel Request Docs');
        $this->openApi['info']['description']     = config('request-docs.open_api.description', 'Laravel Request Docs');
        $this->openApi['info']['license']['name'] = config('request-docs.open_api.license', 'Apache 2.0');
        $this->openApi['info']['license']['url']  = config('request-docs.open_api.license_url', 'https://www.apache.org/licenses/LICENSE-2.0.html');
        $this->openApi['servers'][]               = [
            'url' => config('request-docs.open_api.server_url', config('app.url')),
        ];

        $this->docsToOpenApi($docs);
        $this->appendGlobalSecurityScheme();
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function toJson(): string
    {
        return collect($this->openApi)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return OpenApi
     */
    public function toArray(): array
    {
        return $this->openApi;
    }

    /**
     * @return array<string, array{
     *     description: string,
     *     content: array{
     *         string: array{
     *             schema: array{
     *                 type: string
     *             }
     *         }
     *     }
     * }>
     */
    protected function setAndFilterResponses(Doc $doc): array
    {
        $docResponses    = $doc->getResponses();
        $configResponses = config('request-docs.open_api.responses', []);

        if (count($docResponses) === 0 || count($configResponses) === 0) {
            return $configResponses;
        }

        $rtn = [];

        foreach ($docResponses as $responseCode) {
            $rtn[$responseCode] = $configResponses[$responseCode] ?? $configResponses['default'] ?? [];
        }

        return $rtn;
    }

    protected function attributeIsFile(string $rule): bool
    {
        return str_contains($rule, 'file') || str_contains($rule, 'image');
    }

    /**
     * @return Parameter
     */
    protected function makeQueryParameterItem(string $attribute, string $rule): array
    {
        return [
            'name'        => $attribute,
            'description' => $rule,
            'in'          => 'query',
            'style'       => 'form',
            'required'    => str_contains($rule, 'required'),
            'schema'      => [
                'type' => $this->getAttributeType($rule),
            ],
        ];
    }

    /**
     * @param  string[]  $rule
     * @return Parameter
     */
    protected function makePathParameterItem(string $attribute, array $rule): array
    {
        if (is_array($rule)) {
            $rule = implode('|', $rule);
        }

        return [
            'name'        => $attribute,
            'description' => $rule,
            'in'          => 'path',
            'style'       => 'simple',
            'required'    => str_contains($rule, 'required'),
            'schema'      => [
                'type' => $this->getAttributeType($rule),
            ],
        ];
    }

    /**
     * @return RequestBody
     */
    protected function makeRequestBodyItem(string $contentType): array
    {
        return [
            'description' => "Request body",
            'content'     => [
                $contentType => [
                    'schema' => [
                        'type'       => 'object',
                        'properties' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return Property
     */
    protected function makeRequestBodyContentPropertyItem(string $rule): array
    {
        $type = $this->getAttributeType($rule);

        return [
            'type'     => $type,
            'nullable' => str_contains($rule, 'nullable'),
            'format'   => $this->attributeIsFile($rule) ? 'binary' : $type,
        ];
    }

    protected function getAttributeType(string $rule): string
    {
        if (str_contains($rule, 'string') || $this->attributeIsFile($rule)) {
            return 'string';
        }

        if (str_contains($rule, 'array')) {
            return 'array';
        }

        if (str_contains($rule, 'integer')) {
            return 'integer';
        }

        if (str_contains($rule, 'boolean')) {
            return 'boolean';
        }

        return "object";
    }

    protected function appendGlobalSecurityScheme(): void
    {
        $securityType = config('request-docs.open_api.security.type');

        if ($securityType === null) {
            return;
        }

        switch ($securityType) {
            case 'bearer':
                $this->openApi['components']['securitySchemes']['bearerAuth'] = [
                    'type'        => 'http',
                    'name'        => config('request-docs.open_api.security.name', 'Bearer Token'),
                    'description' => 'Http Bearer Authorization Token',
                    'scheme'      => 'bearer',
                ];
                $this->openApi['security'][]                                  = [
                    'bearerAuth' => [],
                ];
                break;

            case 'basic':
                $this->openApi['components']['securitySchemes']['basicAuth'] = [
                    'type'        => 'http',
                    'name'        => config('request-docs.open_api.security.name', 'Basic Username and Password'),
                    'description' => 'Http Basic Authorization Username and Password',
                    'scheme'      => 'basic',
                ];
                $this->openApi['security'][]                                 = [
                    'basicAuth' => [],
                ];
                break;

            case 'apikey':
                $this->openApi['components']['securitySchemes']['apiKeyAuth'] = [
                    'type'        => 'apiKey',
                    'name'        => config('request-docs.open_api.security.name', 'api_key'),
                    'in'          => config('request-docs.open_api.security.position', 'header'),
                    'description' => config('app.name') . ' Provided Authorization Api Key',
                ];
                $this->openApi['security'][]                                  = ['apiKeyAuth' => []];
                break;

            case 'jwt':
                $this->openApi['components']['securitySchemes']['bearerAuth'] = [
                    'type'         => 'http',
                    'scheme'       => 'bearer',
                    'name'         => config('request-docs.open_api.security.name', 'Bearer JWT Token'),
                    'in'           => config('request-docs.open_api.security.position', 'header'),
                    'description'  => 'JSON Web Token',
                    'bearerFormat' => 'JWT',
                ];
                $this->openApi['security'][]                                  = [
                    'bearerAuth' => [],
                ];
                break;

            default:
                break;
        }
    }

    /**
     * @param  \Rakutentech\LaravelRequestDocs\Doc[]  $docs
     */
    // TODO Should reduce complexity
    // phpcs:ignore
    private function docsToOpenApi(array $docs): void
    {
        $this->openApi['paths'] = [];
        $deleteWithBody         = config('request-docs.open_api.delete_with_body', false);
        $excludeHttpMethods     = array_map(static fn ($item) => strtolower($item), config('request-docs.open_api.exclude_http_methods', []));

        foreach ($docs as $doc) {
            $httpMethod = strtolower($doc->getHttpMethod());

            if (in_array($httpMethod, $excludeHttpMethods)) {
                continue;
            }

            $requestHasFile  = false;
            $isGet           = $httpMethod === 'get';
            $isPost          = $httpMethod === 'post';
            $isPut           = $httpMethod === 'put';
            $isDelete        = $httpMethod === 'delete';
            $uriLeadingSlash = '/' . $doc->getUri();

            $this->openApi['paths'][$uriLeadingSlash][$httpMethod]['description'] = $doc->getDocBlock();
            $this->openApi['paths'][$uriLeadingSlash][$httpMethod]['parameters']  = [];

            foreach ($doc->getPathParameters() as $parameter => $rule) {
                $this->openApi['paths'][$uriLeadingSlash][$httpMethod]['parameters'][] = $this->makePathParameterItem($parameter, $rule);
            }

            $this->openApi['paths'][$uriLeadingSlash][$httpMethod]['responses'] = $this->setAndFilterResponses($doc);

            foreach ($doc->getRules() as $attribute => $rules) {
                foreach ($rules as $rule) {
                    if (!$isPost && !$isPut && !$isDelete) {
                        continue;
                    }

                    $requestHasFile = $this->attributeIsFile($rule);

                    if ($requestHasFile) {
                        break 2;
                    }
                }
            }

            $contentType = $requestHasFile ? 'multipart/form-data' : 'application/json';

            if ($isPost || $isPut || ($isDelete && $deleteWithBody)) {
                $this->openApi['paths'][$uriLeadingSlash][$httpMethod]['requestBody'] = $this->makeRequestBodyItem($contentType);
            }

            foreach ($doc->getRules() as $attribute => $rules) {
                foreach ($rules as $rule) {
                    if ($isGet) {
                        $parameter = $this->makeQueryParameterItem($attribute, $rule);

                        $this->openApi['paths'][$uriLeadingSlash][$httpMethod]['parameters'][] = $parameter;
                    }

                    if (!$isPost && !$isPut && (!$isDelete || !$deleteWithBody)) {
                        continue;
                    }

                    $this->openApi['paths'][$uriLeadingSlash][$httpMethod]['requestBody']['content'][$contentType]['schema']['properties'][$attribute] = $this->makeRequestBodyContentPropertyItem($rule);
                }
            }
        }
    }
}
