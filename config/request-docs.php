<?php

return [
    /*
    * Route where request docs will be served from
    * localhost:8080/request-docs
    */
    'url' => 'routes-docs',
    'middlewares' => [
        //Example
        \App\Http\Middleware\NotFoundWhenProduction::class,
    ],
    /**
     * Path to to static HTML if using command line.
     */
    'docs_path' => base_path('docs/request-docs/'),
    'swagger' => [
        /*
        * https://github.com/DarkaOnLine/L5-Swagger
        * File name of the generated json documentation file by Laravel swagger
        */
        'docs_json_path' => storage_path('api-docs/api-docs.json') // change it to null to disable
    ],
    'hide_matching' => [
        "#^telescope#",
        "#^docs#",
        "#^request-docs#",
    ]
];
