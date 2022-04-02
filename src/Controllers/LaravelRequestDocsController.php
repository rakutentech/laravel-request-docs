<?php

namespace Rakutentech\LaravelRequestDocs\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocs;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsToOpenApi;
use Illuminate\Routing\Controller;

class LaravelRequestDocsController extends Controller
{
    private $laravelRequestDocs;

    public function __construct(LaravelRequestDocs $laravelRequestDoc, LaravelRequestDocsToOpenApi $laravelRequestDocsToOpenApi)
    {
        $this->laravelRequestDocs = $laravelRequestDoc;
        $this->laravelRequestDocsToOpenApi = $laravelRequestDocsToOpenApi;
    }

    public function index(Request $request)
    {
        $docs = $this->laravelRequestDocs->getDocs();
        $docs = $this->laravelRequestDocs->sortDocs($docs, config('request-docs.sort_by', 'default'));
        if ($request->openapi) {
            return response()->json(
                $this->laravelRequestDocsToOpenApi->openApi($docs)->toArray(),
                Response::HTTP_OK,
                [
                    'Content-type'=> 'application/json; charset=utf-8'
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }
        return view('request-docs::index')->with(compact('docs'));
    }
}
