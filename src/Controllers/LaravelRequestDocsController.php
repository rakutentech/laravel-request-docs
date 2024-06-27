<?php

namespace Rakutentech\LaravelRequestDocs\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocs;
use Rakutentech\LaravelRequestDocs\LaravelRequestDocsToOpenApi;

class LaravelRequestDocsController extends Controller
{
    private LaravelRequestDocs $laravelRequestDocs;
    private LaravelRequestDocsToOpenApi $laravelRequestDocsToOpenApi;

    public function __construct(LaravelRequestDocs $laravelRequestDoc, LaravelRequestDocsToOpenApi $laravelRequestDocsToOpenApi)
    {
        $this->laravelRequestDocsToOpenApi = $laravelRequestDocsToOpenApi;
        $this->laravelRequestDocs          = $laravelRequestDoc;
    }

    /**
     * @codeCoverageIgnore
     */
    public function index(Request $request): Response
    {
        return response()->view('request-docs::index');
    }

    /**
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function api(Request $request): JsonResponse
    {
        $showGet    = !$request->has('showGet') || $request->input('showGet') === 'true';
        $showPost   = !$request->has('showPost') || $request->input('showPost') === 'true';
        $showPut    = !$request->has('showPut') || $request->input('showPut') === 'true';
        $showPatch  = !$request->has('showPatch') || $request->input('showPatch') === 'true';
        $showDelete = !$request->has('showDelete') || $request->input('showDelete') === 'true';
        $showHead   = !$request->has('showHead') || $request->input('showHead') === 'true';

        // Get a list of Doc with route and rules information.
        // If user defined `Route::match(['get', 'post'], 'uri', ...)`,
        // only a single Doc will be generated.
        $docs = $this->laravelRequestDocs->getDocs(
            $showGet,
            $showPost,
            $showPut,
            $showPatch,
            $showDelete,
            $showHead,
        );

        // Loop and split Doc by the `methods` property.
        // `Route::match([...n], 'uri', ...)` will generate n number of Doc.
        $docs = $this->laravelRequestDocs->splitByMethods($docs);
        $docs = $this->laravelRequestDocs->sortDocs($docs, $request->input('sort'));
        $docs = $this->laravelRequestDocs->groupDocs($docs, $request->input('groupby'));

        if ($request->input('openapi')) {
            return response()->json(
                $this->laravelRequestDocsToOpenApi->openApi($docs->all())->toArray(),
                Response::HTTP_OK,
                [
                    'Content-type' => 'application/json; charset=utf-8',
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            );
        }

        return response()->json(
            $docs,
            Response::HTTP_OK,
            [
                'Content-type' => 'application/json; charset=utf-8',
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }

    /**
     * @codeCoverageIgnore
     */

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
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
            $headers['Expires']       = gmdate('D, d M Y H:i:s \G\M\T', time() + 1800);
            return response()->file($path, $headers);
        }

        return response()->json(['error' => 'file not found'], 404);
    }

    /**
     * @codeCoverageIgnore
     */
    public function config(Request $request): JsonResponse
    {
        $config = [
            'title'           => config('request-docs.title'),
            'default_headers' => config('request-docs.default_headers'),
        ];
        return response()->json($config);
    }
}
