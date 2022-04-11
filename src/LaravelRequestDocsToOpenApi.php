<?php

namespace Rakutentech\LaravelRequestDocs;

use Route;
use ReflectionMethod;
use Illuminate\Support\Str;
use Exception;
use Throwable;

class LaravelRequestDocsToOpenApi
{
    private $openApi = [];

    // docs from $docs = $this->laravelRequestDocs->getDocs();
    public function openApi(array $docs): LaravelRequestDocsToOpenApi
    {
        $this->openApi['openapi']                 = config('request-docs.open_api.version', '3.0.0');
        $this->openApi['info']['version']         = config('request-docs.open_api.document_version', '1.0.0');
        $this->openApi['info']['title']           = config('request-docs.document_name', 'LRD');
        $this->openApi['info']['description']     = config('request-docs.document_name', 'LRD');
        $this->openApi['info']['license']['name'] = config('request-docs.open_api.license', 'Apache 2.0');
        $this->openApi['info']['license']['url']  = config('request-docs.open_api.license_url', 'https://www.apache.org/licenses/LICENSE-2.0.html');
        $this->openApi['servers'][]               = [
            'url' => config('request-docs.open_api.server_url', config('app.url'))
        ];

        $this->docsToOpenApi($docs);
        return $this;
    }

    private function docsToOpenApi(array $docs)
    {
        $this->openApi['paths'] = [];
        foreach ($docs as $doc) {
            $requestHasFile = false;
            $httpMethod = strtolower($doc['httpMethod']);
            $isGet    = $httpMethod == 'get';
            $isPost   = $httpMethod == 'post';
            $isPut    = $httpMethod == 'put';
            $isDelete = $httpMethod == 'delete';

            $this->openApi['paths'][$doc['uri']][$httpMethod]['description'] = $doc['docBlock'];
            $this->openApi['paths'][$doc['uri']][$httpMethod]['parameters'] = [];

            $this->openApi['paths'][$doc['uri']][$httpMethod]['responses'] = config('request-docs.open_api.responses', []);

            foreach ($doc['rules'] as $attribute => $rules) {
                foreach ($rules as $rule) {
                    if ($isPost || $isPut || $isDelete) {
                        $requestHasFile = $this->attributeIsFile($rule);

                        if ($requestHasFile) {
                            break 2;
                        }
                    }
                }
            }

            $contentType = $requestHasFile ? 'multipart/form-data' : 'application/json';

            if ($isGet) {
                $this->openApi['paths'][$doc['uri']][$httpMethod]['parameters'] = [];
            }
            if ($isPost || $isPut || $isDelete) {
                $this->openApi['paths'][$doc['uri']][$httpMethod]['requestBody'] = $this->makeRequestBodyItem($contentType);
            }

            foreach ($doc['rules'] as $attribute => $rules) {
                foreach ($rules as $rule) {
                    if ($isGet) {
                        $parameter = $this->makeQueryParameterItem($attribute, $rule);
                        $this->openApi['paths'][$doc['uri']][$httpMethod]['parameters'][] = $parameter;
                    }
                    if ($isPost || $isPut || $isDelete) {
                        $this->openApi['paths'][$doc['uri']][$httpMethod]['requestBody']['content'][$contentType]['schema']['properties'][$attribute] = $this->makeRequestBodyContentPropertyItem($rule);
                    }
                }
            }
        }
    }

    protected function attributeIsFile(string $rule)
    {
        return str_contains($rule, 'file') || str_contains($rule, 'image');
    }

    protected function makeQueryParameterItem(string $attribute, string $rule): array
    {
        $parameter = [
            'name'        => $attribute,
            'description' => $rule,
            'in'          => 'query',
            'style'       => 'form',
            'required'    => str_contains($rule, 'required'),
            'schema'      => [
                'type' => $this->getAttributeType($rule),
            ],
        ];
        return $parameter;
    }

    protected function makeRequestBodyItem(string $contentType): array
    {
        $requestBody = [
            'description' => "Request body",
            'content'     => [
                $contentType => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [],
                    ],
                ],
            ],
        ];
        return $requestBody;
    }

    protected function makeRequestBodyContentPropertyItem(string $rule): array
    {
        $type = $this->getAttributeType($rule);

        return [
            'type' => $type,
            'nullable' => str_contains($rule, 'nullable'),
            'format' => $this->attributeIsFile($rule) ? 'binary' : $type,
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

    public function toJson(): string
    {
        return collect($this->openApi)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function toArray(): array
    {
        return $this->openApi;
    }
}
