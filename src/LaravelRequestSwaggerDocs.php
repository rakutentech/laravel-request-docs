<?php

namespace Rakutentech\LaravelRequestDocs;

use Route;
use ReflectionMethod;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LaravelRequestSwaggerDocs
{
    private array $swaggerJson = [
        'paths' => []
    ];


    public function regenerateSwaggerJson(array $docs, string $swaggerJsonPath)
    {
        $this->swaggerJson = json_decode(file_get_contents($swaggerJsonPath), true);
        $swaggerJson = $this->swaggerJson;

        // var_dump($json);
        foreach ($docs as $doc) {
            $swaggerUri = "/" . $doc['uri'];
            $existsSwaggerUri = !empty($swaggerJson['paths'][$swaggerUri]);
            $method = $this->getMethod($doc);
            if ($existsSwaggerUri) {
                $swaggerUri = $swaggerJson['paths'][$swaggerUri];
                $existsSwaggerMethod = !empty($swaggerUri[$method]);
                if ($existsSwaggerMethod) {
                    $this->updatePath($doc);
                    var_dump($doc);
                }
                if (!$existsSwaggerMethod) {
                    $this->addPath($doc);
                    var_dump($doc);
                }
            }
            if (!$existsSwaggerUri) {
                $this->addPath($doc);
            }
            // foreach ($swaggerJson['paths'] as $swaggerUri => $swaggerDoc) {
            //     if ($swaggerUri == $uri || $swaggerUri == "/" .$uri) {
            //         // support only GET and POST for now
            //         // not supporing PUT, DELETE, PATCH, OPTIONS due to in path, or in body parameters mix match
            //         if (!empty($swaggerDoc['get']['parameters'])) {


            //             // foreach ($swaggerDoc['get']['parameters'] as $index => $parameter) {
            //             //     // var_dump($doc['rules']);
            //             //     var_dump($this->swaggerJson['get']['parameters'][$index]);
            //             //     if (empty($doc['rules'][$parameter['name']])) {
            //             //         // $this->swaggerJson[]
            //             //         // var_dump($parameter);
            //             //         // var_dump($doc['rules'][$parameter['name']]);
            //             //     }
            //             // }
            //             // var_dump($swaggerDoc['get']['parameters']);
            //         }
            //         if (!empty($swaggerDoc['post']['requestBody']["content"]["application/json"]['schema']['properties'])) {
            //             // var_dump($swaggerDoc['post']['requestBody']["content"]["application/json"]['schema']['properties']);
            //         }
            //     }
            // }
        }
        return $this->swaggerJson;
    }

    public function addPath(array $doc)
    {
        # code...
    }

    public function updatePath(array $doc)
    {
        # code...
    }

    public function getMethod(array $doc)
    {
        return Str::lower($doc['methods'][0]);
    }
}
