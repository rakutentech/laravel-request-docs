<?php
// config for Rakutentech/ClassName
return [
    // localhost:8080/request-docs
    'url' => 'request-docs',
    'middlewares' => [
        //Example
        // \App\Http\Middleware\NotFoundWhenProduction::class,
    ],
    'docs_path' => base_path('docs/request-docs/')
];
