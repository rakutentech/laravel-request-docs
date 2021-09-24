<?php

namespace Rakutentech\LaravelRequestDocs\Controllers;

use Route;
use Closure;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocs;
use Illuminate\Routing\Controller;

class LaravelRequestDocsController extends Controller
{
    private $laravelRequestDocs;

    public function __construct(LaravelRequestDocs $laravelRequestDocs)
    {
        $this->laravelRequestDocs = $laravelRequestDocs;
    }

    public function index()
    {
        $docs = $this->laravelRequestDocs->getDocs();
        $docs = $this->laravelRequestDocs->sortDocs($docs, config('request-docs.sort_by', 'default'));
        return view('request-docs::index')->with(compact('docs'));
    }

}
