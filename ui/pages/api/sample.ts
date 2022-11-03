// Next.js API route support: https://nextjs.org/docs/api-routes/introduction
import type { NextApiRequest, NextApiResponse } from 'next'

type Data = {
  name: string
}

export default function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
    const response = [
        {
            "uri": "api/chart",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "DashboardController",
            "controller_full_path": "App\\Http\\Controllers\\DashboardController",
            "method": "chart",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/documentation",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "L5Swagger\\Http\\Middleware\\Config"
            ],
            "controller": "SwaggerController",
            "controller_full_path": "\\L5Swagger\\Http\\Controllers\\SwaggerController",
            "method": "api",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/export",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "OrderController",
            "controller_full_path": "App\\Http\\Controllers\\OrderController",
            "method": "export",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/login",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "api"
            ],
            "controller": "AuthController",
            "controller_full_path": "App\\Http\\Controllers\\AuthController",
            "method": "login",
            "httpMethod": "POST",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/logout",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "AuthController",
            "controller_full_path": "App\\Http\\Controllers\\AuthController",
            "method": "logout",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/oauth2-callback",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "L5Swagger\\Http\\Middleware\\Config"
            ],
            "controller": "SwaggerController",
            "controller_full_path": "\\L5Swagger\\Http\\Controllers\\SwaggerController",
            "method": "oauth2Callback",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/orders",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "OrderController",
            "controller_full_path": "App\\Http\\Controllers\\OrderController",
            "method": "index",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/orders/{order}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "OrderController",
            "controller_full_path": "App\\Http\\Controllers\\OrderController",
            "method": "show",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/permissions",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "PermissionController",
            "controller_full_path": "App\\Http\\Controllers\\PermissionController",
            "method": "index",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/products",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "ProductController",
            "controller_full_path": "App\\Http\\Controllers\\ProductController",
            "method": "store",
            "httpMethod": "POST",
            "rules": {
                "image": [
                    "required"
                ],
                "title": [
                    "required"
                ],
                "description": [
                    "required"
                ],
                "price": [
                    "required|numeric"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "api/products",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "ProductController",
            "controller_full_path": "App\\Http\\Controllers\\ProductController",
            "method": "index",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/products/{product}",
            "methods": [
                "DELETE"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "ProductController",
            "controller_full_path": "App\\Http\\Controllers\\ProductController",
            "method": "destroy",
            "httpMethod": "DELETE",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/products/{product}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "ProductController",
            "controller_full_path": "App\\Http\\Controllers\\ProductController",
            "method": "show",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/products/{product}",
            "methods": [
                "PUT",
                "PATCH"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "ProductController",
            "controller_full_path": "App\\Http\\Controllers\\ProductController",
            "method": "update",
            "httpMethod": "PUT",
            "rules": {
                "title": [
                    "required"
                ],
                "description": [
                    "required"
                ],
                "price": [
                    "required|numeric"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "api/register",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "api"
            ],
            "controller": "AuthController",
            "controller_full_path": "App\\Http\\Controllers\\AuthController",
            "method": "register",
            "httpMethod": "POST",
            "rules": {
                "first_name": [
                    "required"
                ],
                "last_name": [
                    "required"
                ],
                "email": [
                    "required"
                ],
                "password": [
                    "required"
                ],
                "password_confirm": [
                    "required|same:password"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "api/roles",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "RoleController",
            "controller_full_path": "App\\Http\\Controllers\\RoleController",
            "method": "store",
            "httpMethod": "POST",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/roles",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "RoleController",
            "controller_full_path": "App\\Http\\Controllers\\RoleController",
            "method": "index",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/roles/{role}",
            "methods": [
                "DELETE"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "RoleController",
            "controller_full_path": "App\\Http\\Controllers\\RoleController",
            "method": "destroy",
            "httpMethod": "DELETE",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/roles/{role}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "RoleController",
            "controller_full_path": "App\\Http\\Controllers\\RoleController",
            "method": "show",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/roles/{role}",
            "methods": [
                "PUT",
                "PATCH"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "RoleController",
            "controller_full_path": "App\\Http\\Controllers\\RoleController",
            "method": "update",
            "httpMethod": "PUT",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/upload",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "ImageController",
            "controller_full_path": "App\\Http\\Controllers\\ImageController",
            "method": "upload",
            "httpMethod": "POST",
            "rules": {
                "image": [
                    "required|mimes:jpg,jpeg,gif,png|max:8192"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "api/user",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "user",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/users",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "store",
            "httpMethod": "POST",
            "rules": {
                "first_name": [
                    "required"
                ],
                "last_name": [
                    "required"
                ],
                "email": [
                    "required|email|unique:users"
                ],
                "role_id": [
                    "required"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "api/users",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "index",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/users/info",
            "methods": [
                "PUT"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "updateInfo",
            "httpMethod": "PUT",
            "rules": {
                "password": [
                    "required"
                ],
                "password_confirm": [
                    "required|same:password"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "api/users/password",
            "methods": [
                "PUT"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "updatePassword",
            "httpMethod": "PUT",
            "rules": {
                "password": [
                    "required"
                ],
                "password_confirm": [
                    "required|same:password"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "api/users/{user}",
            "methods": [
                "DELETE"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "destroy",
            "httpMethod": "DELETE",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/users/{user}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "show",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "api/users/{user}",
            "methods": [
                "PUT",
                "PATCH"
            ],
            "middlewares": [
                "api",
                "auth:api"
            ],
            "controller": "UserController",
            "controller_full_path": "App\\Http\\Controllers\\UserController",
            "method": "update",
            "httpMethod": "PUT",
            "rules": {
                "first_name": [
                    "required"
                ],
                "last_name": [
                    "required"
                ],
                "email": [
                    "required|email"
                ],
                "role_id": [
                    "required"
                ]
            },
            "docBlock": ""
        },
        {
            "uri": "oauth/authorize",
            "methods": [
                "DELETE"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "DenyAuthorizationController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\DenyAuthorizationController",
            "method": "deny",
            "httpMethod": "DELETE",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/authorize",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "ApproveAuthorizationController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\ApproveAuthorizationController",
            "method": "approve",
            "httpMethod": "POST",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/authorize",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "AuthorizationController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\AuthorizationController",
            "method": "authorize",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/clients",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "ClientController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\ClientController",
            "method": "store",
            "httpMethod": "POST",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/clients",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "ClientController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\ClientController",
            "method": "forUser",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/clients/{client_id}",
            "methods": [
                "DELETE"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "ClientController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\ClientController",
            "method": "destroy",
            "httpMethod": "DELETE",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/clients/{client_id}",
            "methods": [
                "PUT"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "ClientController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\ClientController",
            "method": "update",
            "httpMethod": "PUT",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/personal-access-tokens",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "PersonalAccessTokenController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController",
            "method": "store",
            "httpMethod": "POST",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/personal-access-tokens",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "PersonalAccessTokenController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController",
            "method": "forUser",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/personal-access-tokens/{token_id}",
            "methods": [
                "DELETE"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "PersonalAccessTokenController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController",
            "method": "destroy",
            "httpMethod": "DELETE",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/scopes",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "ScopeController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\ScopeController",
            "method": "all",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/token",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "throttle"
            ],
            "controller": "AccessTokenController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\AccessTokenController",
            "method": "issueToken",
            "httpMethod": "POST",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/token/refresh",
            "methods": [
                "POST"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "TransientTokenController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\TransientTokenController",
            "method": "refresh",
            "httpMethod": "POST",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/tokens",
            "methods": [
                "GET",
                "HEAD"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "AuthorizedAccessTokenController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController",
            "method": "forUser",
            "httpMethod": "GET",
            "rules": [],
            "docBlock": ""
        },
        {
            "uri": "oauth/tokens/{token_id}",
            "methods": [
                "DELETE"
            ],
            "middlewares": [
                "web",
                "auth"
            ],
            "controller": "AuthorizedAccessTokenController",
            "controller_full_path": "\\Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController",
            "method": "destroy",
            "httpMethod": "DELETE",
            "rules": [],
            "docBlock": ""
        }
    ];
    res.status(200).json(JSON.parse(JSON.stringify(response)));
}
