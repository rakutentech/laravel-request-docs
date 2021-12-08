<?php

return [
    'document_name'  => 'LRD',

    /*
    * Route where request docs will be served from
    * localhost:8080/request-docs
    */
    'url' => 'request-docs',
    'middlewares' => [
        //Example
        // \App\Http\Middleware\NotFoundWhenProduction::class,
    ],
    /**
     * Path to to static HTML if using command line.
     */
    'docs_path' => base_path('docs/request-docs/'),

    /**
     * Sorting route by and there is two types default(route methods), route_names.
     */
    'sort_by' => 'default',

    //Use only routes where ->uri start with next string Using Str::startWith( . e.g. - /api/mobile
    'only_route_uri_start_with' => '',

    'hide_matching' => [
        "#^telescope#",
        "#^docs#",
        "#^request-docs#",
    ]
];
