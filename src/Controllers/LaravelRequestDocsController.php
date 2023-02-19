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
        return view('request-docs::index');
    }
    public function api(Request $request)
    {
        $docs = $this->laravelRequestDocs->getDocs();
        $docs = $this->laravelRequestDocs->sortDocs($docs, $request->sort);
        $docs = $this->laravelRequestDocs->groupDocs($docs, $request->groupby);

        $showGet = $request->has('showGet') ? $request->showGet == 'true' : true;
        $showPost = $request->has('showPost') ? $request->showPost == 'true' : true;
        $showPut = $request->has('showPut') ? $request->showPut == 'true' : true;
        $showPatch = $request->has('showPatch') ? $request->showPatch == 'true' : true;
        $showDelete = $request->has('showDelete') ? $request->showDelete == 'true' : true;
        $showHead = $request->has('showHead') ? $request->showHead == 'true' : true;

        $docs = $this->laravelRequestDocs->filterByMethods(
            $docs,
            $showGet,
            $showPost,
            $showPut,
            $showPatch,
            $showDelete,
            $showHead
        );
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

        return response()->json(
            $docs,
            Response::HTTP_OK,
            [
                'Content-type'=> 'application/json; charset=utf-8',
                'Cache-Control' => 'public, max-age=60',
                'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 60),
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    public function assets(Request $request)
    {
        $path = explode('/', $request->path());
        $path = end($path);
        // read js, css from dist folder
        $path = base_path() . "/vendor/rakutentech/laravel-request-docs/resources/dist/_astro/" . $path;
        if (file_exists($path)) {
            $headers = ['Content-Type' => 'text/plain'];
            // set MIME type to js module
            if (str_ends_with($path, '.js')) {
                $headers = ['Content-Type' => 'application/javascript'];
            }
            if (str_ends_with($path, '.css')) {
                $headers = ['Content-Type' => 'text/css'];
            }
            if (str_ends_with($path, '.woff')) {
                $headers = ['Content-Type' => 'font/woff'];
            }
            if (str_ends_with($path, '.woff2')) {
                $headers = ['Content-Type' => 'font/woff2'];
            }
            if (str_ends_with($path, '.png')) {
                $headers = ['Content-Type' => 'image/png'];
            }
            if (str_ends_with($path, '.jpg')) {
                $headers = ['Content-Type' => 'image/jpg'];
            }

            // set cache control headers
            $headers['Cache-Control'] = 'public, max-age=1800';
            $headers['Expires'] = gmdate('D, d M Y H:i:s \G\M\T', time() + 1800);
            return response()->file($path, $headers);
        }
        return response()->json(['error' => 'file not found'], 404);
    }
}
