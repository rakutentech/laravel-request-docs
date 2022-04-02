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
            $httpMethod = strtolower($doc['httpMethod']);
            $isGet    = $httpMethod == 'get';
            $isPost   = $httpMethod == 'post';
            $isPut    = $httpMethod == 'put';
            $isDelete = $httpMethod == 'delete';

            $this->openApi['paths'][$doc['uri']][$httpMethod]['description'] = $doc['docBlock'];
            $this->openApi['paths'][$doc['uri']][$httpMethod]['parameters'] = [];

            $this->openApi['paths'][$doc['uri']][$httpMethod]['responses'] = config('request-docs.open_api.responses', []);

            if ($isGet) {
                $this->openApi['paths'][$doc['uri']][$httpMethod]['parameters'] = [];
            }
            if ($isPost || $isPut || $isDelete) {
                $this->openApi['paths'][$doc['uri']][$httpMethod]['requestBody'] = $this->makeRequestBodyItem();
            }
            foreach ($doc['rules'] as $attribute => $rules) {
                foreach ($rules as $rule) {
                    if ($isGet) {
                        $parameter = $this->makeQueryParameterItem($attribute, $rule);
                        $this->openApi['paths'][$doc['uri']][$httpMethod]['parameters'][] = $parameter;
                    }
                    if ($isPost || $isPut || $isDelete) {
                        $this->openApi['paths'][$doc['uri']][$httpMethod]['requestBody']['content']['application/json']['schema']['properties'][$attribute] = $this->makeRequestBodyContentPropertyItem($rule);
                    }
                }
            }
        }
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

    protected function makeRequestBodyItem(): array
    {
        $requestBody = [
            'description' => "Request body",
            'content'     => [
                'application/json' => [
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
        return [
            'type' => $this->getAttributeType($rule),
        ];
    }


    protected function getAttributeType(string $rule): string
    {
        if (str_contains($rule, 'string')) {
            return 'string';
        }
        if (str_contains($rule, 'array')) {
            return 'array';
        }
        if (str_contains($rule, 'integer')) {
            return 'integer';
        }
        return "object";
    }

    public function toJson() : string {
        return collect($this->openApi)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function toArray() : array {
        return $this->openApi;
    }
}
