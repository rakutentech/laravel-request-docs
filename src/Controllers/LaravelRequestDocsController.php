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
        $baseDirectory = base_path() . "/vendor/rakutentech/laravel-request-docs/resources/dist/_astro";

        // Sanitize filename and block traversal
        $path = basename((string) $path);

        // Whitelist extensions (security hardening)
        $allowed = [
            'js'   => 'application/javascript',
            'css'  => 'text/css',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === '' || !array_key_exists($ext, $allowed)) {
            return response()->json(['error' => 'file not found'], 404);
        }

        // Build candidate path and ensure it stays under _astro
        $candidate = $baseDirectory . DIRECTORY_SEPARATOR . $path;

        $baseReal = realpath($baseDirectory);
        $fileReal = realpath($candidate);

        if ($baseReal === false || $fileReal === false) {
            return response()->json(['error' => 'file not found'], 404);
        }

        $prefix = $baseReal . DIRECTORY_SEPARATOR;
        if (strncmp($fileReal, $prefix, strlen($prefix)) !== 0) {
            return response()->json(['error' => 'file not found'], 404);
        }

        if (is_file($fileReal)) {
            $headers = ['Content-Type' => $allowed[$ext]];

            // set cache control headers
            $headers['Cache-Control'] = 'public, max-age=1800';
            $headers['Expires']       = gmdate('D, d M Y H:i:s \G\M\T', time() + 1800);

            return response()->file($fileReal, $headers);
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