<?php

namespace Rakutentech\LaravelRequestDocs\Commands;

use Illuminate\Console\Command;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocs;
use Rakutentech\LaravelRequestDocs\LaravelRequestSwaggerDocs;
use File;

class LaravelRequestDocsSwaggerCommand extends Command
{
    public $signature = 'lrd:swagger';

    public $description = 'Generate request docs to HTML and update swagger json';

    private $laravelRequestDocs;
    private $laravelRequestSwaggerDocs;

    public function __construct(LaravelRequestDocs $laravelRequestDocs, LaravelRequestSwaggerDocs $laravelRequestSwaggerDocs)
    {
        $this->laravelRequestDocs        = $laravelRequestDocs;
        $this->laravelRequestSwaggerDocs = $laravelRequestSwaggerDocs;
        parent::__construct();
    }

    public function handle()
    {
        $swaggerJsonPath = config('request-docs.swagger.docs_json_path');
        $swaggerJsonPathDst = $swaggerJsonPath;

        $docs = $this->laravelRequestDocs->getDocs();

        if (! File::exists(dirname($swaggerJsonPathDst))) {
            File::makeDirectory(dirname($swaggerJsonPathDst), 0755, true);
        }

        // start swagger json update
        $swaggerJson = $this->laravelRequestSwaggerDocs->regenerateSwaggerJson($docs, $swaggerJsonPath);
        File::put($swaggerJsonPathDst, json_encode($swaggerJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->comment("Swagger Docs updated: $swaggerJsonPath");
    }
}
