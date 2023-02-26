<?php

namespace Rakutentech\LaravelRequestDocs\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Rakutentech\LaravelRequestDocs\Doc;
use Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController;

class LRDTest extends TestCase
{
    public function testGetDocs()
    {
//        Route::get('closure', function () {
//            return true;
//        });

        Route::get('users', UserController::class);

        $docs = $this->lrd->getDocs();

        $docSize  = 10;
        $firstDoc = $docs[0]->toArray();

        $this->assertCount($docSize, $firstDoc);
        $this->assertArrayHasKey('uri', $firstDoc);
        $this->assertArrayHasKey('methods', $firstDoc);
        $this->assertArrayHasKey('middlewares', $firstDoc);
        $this->assertArrayHasKey('controller', $firstDoc);
        $this->assertArrayHasKey('controller_full_path', $firstDoc);
        $this->assertArrayHasKey('method', $firstDoc);
        $this->assertArrayHasKey('httpMethod', $firstDoc);
        $this->assertArrayHasKey('rules', $firstDoc);
        $this->assertArrayHasKey('docBlock', $firstDoc);
        $this->assertArrayHasKey('responses', $firstDoc);

        $expected = [
            [
                'uri'                  => '/',
                'methods'              => [
                    'GET',
                    'HEAD'
                ],
                'middlewares'          => [],
                'controller'           => 'WelcomeController',
                'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                'method'               => 'index',
                'httpMethod'           => 'GET',
                'rules'                => [
                    'name'     => [
                        'nullable|string|min:5|max:255'
                    ],
                    'title'    => [
                        'Rakutentech\LaravelRequestDocs\Tests\TestRules\Uppercase'
                    ],
                    'file'     => [
                        'file'
                    ],
                    'image'    => [
                        'image'
                    ],
                    'page'     => [
                        'nullable|integer|min:1'
                    ],
                    'per_page' => [
                        'nullable|integer|min:1|max:100'
                    ]
                ],
                'docBlock'             => '#Hello markdown
## Documentation for /my route
',
                'responses'            => [
                    '200',
                    '400',
                    '401',
                    '403',
                    '404',
                    '405',
                    '422',
                    '429',
                    '500',
                    '503'
                ]
            ],
            [
                'uri'                  => 'welcome',
                'methods'              => [
                    'GET',
                    'HEAD'
                ],
                'middlewares'          => [],
                'controller'           => 'WelcomeController',
                'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                'method'               => 'index',
                'httpMethod'           => 'GET',
                'rules'                => [
                    'name'     => [
                        'nullable|string|min:5|max:255'
                    ],
                    'title'    => [
                        'Rakutentech\LaravelRequestDocs\Tests\TestRules\Uppercase'
                    ],
                    'file'     => [
                        'file'
                    ],
                    'image'    => [
                        'image'
                    ],
                    'page'     => [
                        'nullable|integer|min:1'
                    ],
                    'per_page' => [
                        'nullable|integer|min:1|max:100'
                    ]
                ],
                'docBlock'             => '#Hello markdown
## Documentation for /my route
',
                'responses'            => [
                    '200',
                    '400',
                    '401',
                    '403',
                    '404',
                    '405',
                    '422',
                    '429',
                    '500',
                    '503'
                ]
            ],
            [
                'uri'                  => 'welcome',
                'methods'              => [
                    'POST'
                ],
                'middlewares'          => [
                    'auth:api'
                ],
                'controller'           => 'WelcomeController',
                'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                'method'               => 'store',
                'httpMethod'           => 'POST',
                'rules'                => [
                    'error'         => [
                        'string',
                        'exists:'
                    ],
                    'message_param' => [
                        'nullable|string'
                    ]
                ],
                'docBlock'             => '',
                'responses'            => [
                    '200',
                    '400',
                    '401',
                    '403',
                    '404',
                    '405',
                    '422',
                    '429',
                    '500',
                    '503'
                ]
            ],
            [
                'uri'                  => 'welcome',
                'methods'              => [
                    'PUT'
                ],
                'middlewares'          => [],
                'controller'           => 'WelcomeController',
                'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                'method'               => 'edit',
                'httpMethod'           => 'PUT',
                'rules'                => [
                    'message_param'  => [
                        'nullable|string'
                    ],
                    'search_string'  => [
                        'string'
                    ],
                    'search_array'   => [
                        'array'
                    ],
                    'search_integer' => [
                        'integer'
                    ],
                    'search_boolean' => [
                        'boolean'
                    ]
                ],
                'docBlock'             => '',
                'responses'            => [
                    '200',
                    '400',
                    '401',
                    '403',
                    '404',
                    '405',
                    '422',
                    '429',
                    '500',
                    '503'
                ]
            ],
            [
                'uri'                  => 'welcome',
                'methods'              => [
                    'DELETE'
                ],
                'middlewares'          => [],
                'controller'           => 'WelcomeController',
                'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                'method'               => 'destroy',
                'httpMethod'           => 'DELETE',
                'rules'                => [
                    'message_param' => [
                        'nullable|string'
                    ]
                ],
                'docBlock'             => '',
                'responses'            => [
                    '200',
                    '400',
                    '401',
                    '403',
                    '404',
                    '405',
                    '422',
                    '429',
                    '500',
                    '503'
                ]
            ],
            [
                'uri'                  => 'single',
                'methods'              => [
                    'GET',
                    'HEAD'
                ],
                'middlewares'          => [],
                'controller'           => 'SingleActionController',
                'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\SingleActionController',
                'method'               => '__invoke',
                'httpMethod'           => 'GET',
                'rules'                => [],
                'docBlock'             => '',
                'responses'            => [
                    '200',
                    '400',
                    '401',
                    '403',
                    '404',
                    '405',
                    '422',
                    '429',
                    '500',
                    '503'
                ]
            ],
            [
                'uri'                  => 'users',
                'methods'              => [
                    'GET',
                    'HEAD'
                ],
                'middlewares'          => [],
                'controller'           => 'UserController',
                'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController',
                'method'               => '__invoke',
                'httpMethod'           => 'GET',
                'rules'                => [],
                'docBlock'             => '',
                'responses'            => [
                    '200',
                    '400',
                    '401',
                    '403',
                    '404',
                    '405',
                    '422',
                    '429',
                    '500',
                    '503'
                ]
            ]
        ];

        $this->assertSame($expected, collect($docs)->toArray());
    }

    public function testDocsCanFetchAllMethods()
    {
        $docs    = $this->lrd->getDocs();
        $methods = [];
        foreach ($docs as $doc) {
            $methods = array_merge($methods, $doc->getMethods());
        }
        $methods = array_unique($methods);
        sort($methods);
        $this->assertSame(['DELETE', 'GET', 'HEAD', 'POST', 'PUT'], $methods);
    }

    public function testOnlyRouteURIStartWith()
    {
        Config::set('request-docs.only_route_uri_start_with', 'welcome');
        $docs = $this->lrd->getDocs();
        foreach ($docs as $doc) {
            $this->assertStringStartsWith('welcome', $doc->getUri());
        }
    }

    public function testGroupByURI()
    {
        Route::get('users', UserController::class);
        Route::post('users', UserController::class);
        Route::put('users/update', UserController::class);
        Route::put('api/users/', UserController::class);
        Route::put('api/users/{id}', UserController::class);
        Route::put('api/users_roles/{id}', UserController::class);
        Route::put('api/v1/users', UserController::class);
        Route::put('api/v1/users/{id}/store', UserController::class);
        Route::put('api/v2/users', UserController::class);
        Route::put('api/v99/users', UserController::class);

        $docs = $this->lrd->getDocs();
        $docs = $this->lrd->groupDocs($docs, 'api_uri');

        $grouped = collect($docs)
            ->map(function (Doc $item) {
                return collect($item)->only(['uri', 'group', 'group_index', 'httpMethod'])->toArray();
            })
            ->groupBy('group');

        $expected = [
            ''                => [
                [
                    'uri'         => '/',
                    'httpMethod'  => 'GET',
                    'group'       => '',
                    'group_index' => 0
                ]
            ],
            'api/users'       => [
                [
                    'uri'         => 'api/users',
                    'httpMethod'  => 'PUT',
                    'group'       => 'api/users',
                    'group_index' => 0
                ],
                [
                    'uri'         => 'api/users/{id}',
                    'httpMethod'  => 'PUT',
                    'group'       => 'api/users',
                    'group_index' => 1
                ]
            ],
            'api/users_roles' => [
                [
                    'uri'         => 'api/users_roles/{id}',
                    'httpMethod'  => 'PUT',
                    'group'       => 'api/users_roles',
                    'group_index' => 0
                ]
            ],
            'api/v1/users'    => [
                [
                    'uri'         => 'api/v1/users',
                    'httpMethod'  => 'PUT',
                    'group'       => 'api/v1/users',
                    'group_index' => 0
                ],
                [
                    'uri'         => 'api/v1/users/{id}/store',
                    'httpMethod'  => 'PUT',
                    'group'       => 'api/v1/users',
                    'group_index' => 1
                ]
            ],
            'api/v2/users'    => [
                [
                    'uri'         => 'api/v2/users',
                    'httpMethod'  => 'PUT',
                    'group'       => 'api/v2/users',
                    'group_index' => 0
                ]
            ],
            'api/v99/users'   => [
                [
                    'uri'         => 'api/v99/users',
                    'httpMethod'  => 'PUT',
                    'group'       => 'api/v99/users',
                    'group_index' => 0
                ]
            ],
            'single'          => [
                [
                    'uri'         => 'single',
                    'httpMethod'  => 'GET',
                    'group'       => 'single',
                    'group_index' => 0
                ]
            ],
            'users'           => [
                [
                    'uri'         => 'users',
                    'httpMethod'  => 'GET',
                    'group'       => 'users',
                    'group_index' => 0
                ],
                [
                    'uri'         => 'users',
                    'httpMethod'  => 'POST',
                    'group'       => 'users',
                    'group_index' => 1
                ],
                [
                    'uri'         => 'users/update',
                    'httpMethod'  => 'PUT',
                    'group'       => 'users',
                    'group_index' => 2
                ]
            ],
            'welcome'         => [
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'GET',
                    'group'       => 'welcome',
                    'group_index' => 0
                ],
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'POST',
                    'group'       => 'welcome',
                    'group_index' => 1
                ],
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'PUT',
                    'group'       => 'welcome',
                    'group_index' => 2
                ],
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'DELETE',
                    'group'       => 'welcome',
                    'group_index' => 3
                ]
            ],
        ];
        $this->assertSame($expected, $grouped->toArray());
    }

    public function testGroupByURISorted()
    {
        // Define routes with random ordering.
        Route::post('api/v1/users/store', UserController::class);
        Route::get('api/v1/users', UserController::class);

        Route::post('api/v1/health', UserController::class);

        Route::put('api/v1/users/update', UserController::class);
        Route::delete('api/v1/users/destroy', UserController::class);

        Route::get('api/v1/health', UserController::class);

        $docs = $this->lrd->getDocs();
        $docs = $this->lrd->groupDocs($docs, 'api_uri');

        $sorted = collect($docs)
            ->filter(function (Doc $doc) {
                return in_array($doc->getGroup(), ['api/v1/users', 'api/v1/health']);
            })
            ->map(function (Doc $doc) {
                return collect($doc)->only(['uri', 'group', 'group_index'])->toArray();
            })
            ->values();

        $expected = [
            [
                'uri'         => 'api/v1/health',
                'group'       => 'api/v1/health',
                'group_index' => 0
            ],
            [
                'uri'         => 'api/v1/health',
                'group'       => 'api/v1/health',
                'group_index' => 1
            ],
            [
                'uri'         => 'api/v1/users/store',
                'group'       => 'api/v1/users',
                'group_index' => 0
            ],
            [
                'uri'         => 'api/v1/users',
                'group'       => 'api/v1/users',
                'group_index' => 1
            ],
            [
                'uri'         => 'api/v1/users/update',
                'group'       => 'api/v1/users',
                'group_index' => 2
            ],
            [
                'uri'         => 'api/v1/users/destroy',
                'group'       => 'api/v1/users',
                'group_index' => 3
            ]
        ];
        $this->assertSame($expected, $sorted->toArray());
    }

    public function testGroupByURIBackwardCompatible()
    {
        // Set to `null` to test backward compatibility.
        Config::set('request-docs.group_by.uri_patterns', []);

        $docs    = $this->lrd->getDocs();
        $docs    = $this->lrd->groupDocs($docs, 'api_uri');
        $grouped = collect($docs)
            ->map(function (Doc $item) {
                return collect($item)->only(['uri', 'group', 'group_index', 'httpMethod'])->toArray();
            })
            ->groupBy('group');

        $expected = [
            ''        => [
                [
                    'uri'         => '/',
                    'httpMethod'  => 'GET',
                    'group'       => '',
                    'group_index' => 0
                ]
            ],
            'single'  => [
                [
                    'uri'         => 'single',
                    'httpMethod'  => 'GET',
                    'group'       => 'single',
                    'group_index' => 0
                ]
            ],
            'welcome' => [
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'GET',
                    'group'       => 'welcome',
                    'group_index' => 0
                ],
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'POST',
                    'group'       => 'welcome',
                    'group_index' => 1
                ],
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'PUT',
                    'group'       => 'welcome',
                    'group_index' => 2
                ],
                [
                    'uri'         => 'welcome',
                    'httpMethod'  => 'DELETE',
                    'group'       => 'welcome',
                    'group_index' => 3
                ]
            ],
        ];
        $this->assertSame($expected, $grouped->toArray());
    }

    public function testGroupByFQController()
    {
        Route::get('users', UserController::class);
        Route::post('users', UserController::class);
        Route::put('users/update', UserController::class);
        $docs = $this->lrd->getDocs();
        $docs = $this->lrd->groupDocs($docs, 'controller_full_path');

        $grouped = collect($docs)
            ->map(function (Doc $item) {
                return collect($item)->only(['controller_full_path', 'group', 'group_index', 'httpMethod'])->toArray();
            })
            ->groupBy('group');

        $expected = [
            'Rakutentech\LaravelRequestDocs\Tests\TestControllers\SingleActionController' => [
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\SingleActionController',
                    'httpMethod'           => 'GET',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\SingleActionController',
                    'group_index'          => 0
                ]
            ],
            'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController'         => [
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController',
                    'httpMethod'           => 'GET',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController',
                    'group_index'          => 0
                ],
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController',
                    'httpMethod'           => 'POST',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController',
                    'group_index'          => 1
                ],
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController',
                    'httpMethod'           => 'PUT',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\UserController',
                    'group_index'          => 2
                ]
            ],
            'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController'      => [
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'httpMethod'           => 'GET',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'group_index'          => 0
                ],
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'httpMethod'           => 'GET',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'group_index'          => 1
                ],
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'httpMethod'           => 'POST',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'group_index'          => 2
                ],
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'httpMethod'           => 'PUT',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'group_index'          => 3
                ],
                [
                    'controller_full_path' => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'httpMethod'           => 'DELETE',
                    'group'                => 'Rakutentech\LaravelRequestDocs\Tests\TestControllers\WelcomeController',
                    'group_index'          => 4
                ]
            ],
        ];
        $this->assertSame($expected, $grouped->toArray());
    }
}
