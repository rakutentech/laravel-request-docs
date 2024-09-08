<p align="center">
  <a href="https://github.com/rakutentech/laravel-request-docs">
    <img alt="Laravel Request Docs" src="https://imgur.com/NuQNx8v.png">
  </a>
</p>

<p align="center">
  The Hassle-Free automatic API documentation generation for Laravel.
  <br>
  A Swagger alternative.
  <br>
  Supports Open API 3.0.0
  <br>
  <br>
  <b>
     <a href="https://rakutentech.github.io/laravel-request-docs/?api=https://raw.githubusercontent.com/rakutentech/laravel-request-docs/master/ui/public/sample.json" target="_blank">Try latest DEMO!</a>
  </b>
  <br>
  <br>
</p>

<p align="center">
  <img src="https://github.com/rakutentech/laravel-request-docs/actions/workflows/node.yml/badge.svg?branch=master" alt="CI Node">
  <img src="https://github.com/rakutentech/laravel-request-docs/actions/workflows/phptest.yml/badge.svg?branch=master" alt="CI PHP">
</p>

![npm-install-time](https://coveritup.app/badge?org=rakutentech&repo=laravel-request-docs&branch=master&type=npm-install-time)
![composer-install-time](https://coveritup.app/badge?org=rakutentech&repo=laravel-request-docs&branch=master&type=composer-install-time)
![phpunit-run-time](https://coveritup.app/badge?org=rakutentech&repo=laravel-request-docs&branch=master&type=phpunit-run-time)
![phpunit-coverage](https://coveritup.app/badge?org=rakutentech&repo=laravel-request-docs&branch=master&type=phpunit-coverage)

![npm-install-time](https://coveritup.app/chart?org=rakutentech&repo=laravel-request-docs&branch=master&type=npm-install-time&theme=light&line=fill&width=150&height=150&output=svg&line=fill)
![composer-install-time](https://coveritup.app/chart?org=rakutentech&repo=laravel-request-docs&branch=master&type=composer-install-time&theme=light&line=fill&width=150&height=150&output=svg&line=fill)
![phpunit-run-time](https://coveritup.app/chart?org=rakutentech&repo=laravel-request-docs&branch=master&type=phpunit-run-time&theme=light&line=fill&width=150&height=150&output=svg&line=fill)
![phpunit-coverage](https://coveritup.app/chart?org=rakutentech&repo=laravel-request-docs&branch=master&type=phpunit-coverage&theme=light&line=fill&width=150&height=150&output=svg&line=fill)

**Fast** Install on any Laravel Project

**Hassle Free** Auto Generate API Documentation for request rules and parameters

**Analyze** Inbuilt SQL query time analyzer, response time and headers output.

**Supports** Postman and OpenAPI 3.0.0 exports.

## Features

- Light and Dark mode
- Automatic rules fetching from injected Request and by regexp
- Automatic routes fetching from Laravel Routes
- Support for Laravel logs
- Support for SQL query and query time
- Support for HTTP response time and memory consumption
- Support for Authorization Headers
- Support for File uploads
- Support for Eloquents events
- Display extra documentation using markdown
- Saves history previous requests
- Added filters to sort, group and filter routes by methods, controllers, middlewares, routes
- Export Laravel API, routes, rules and documentation to Postman and OpenAPI 3.0.0

# Read on Medium

Automatically generate API documentation for Laravel without writing annotations.

Read more: https://medium.com/web-developer/laravel-automatically-generate-api-documentation-without-annotations-a-swagger-alternative-e0699409a59e

## Requirements

| Lang    | Versions                  |
| :------ |:--------------------------|
| PHP     | 7.4 or 8.0 or 8.1 or 8.2  |
| Laravel | 6.* or 8.* or 9.* or 10.* |

# Installation

You can install the package via composer:

```bash
composer require rakutentech/laravel-request-docs
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag=request-docs-config
php artisan route:cache

# Optional publish assets
# php artisan vendor:publish --tag=request-docs-assets
```

(optional) Add the following middleware to your API, so that the SQL logs and model events are captured.

`app/Http/Kernel.php`

```sh
        'api' => [
            ...
            \Rakutentech\LaravelRequestDocs\LaravelRequestDocsMiddleware::class,
            ... and so on

```

# Usage

## Dashboard

View in the browser on ``/request-docs/``

# Design pattern

For this plugin to work, you need to follow the design pattern by injecting the request class inside the controller.
For extra documentation you can use markdown inside your controller method as well.

![Design pattern](https://imgur.com/yXjq3jp.png)

# Screenshots

**Dark and Light Modes**

<p float="left">
  <img src="https://imgur.com/vOMMYVl.png" width="49%" />
  <img src="https://imgur.com/HZvNOFm.png" width="49%" />
</p>


- Uses local storage to save the history of previous requests and request headers.
- Request, SQL, response and events timeline below:

<p float="left">
  <img src="https://imgur.com/fd09jw1.png" width="32%" />
  <img src="https://imgur.com/8PLLlHv.png" width="45%" />
</p>

<p float="left">
  <img src="https://imgur.com/q3d7pw2.png" width="49%" />
  <img src="https://imgur.com/AHTCUOJ.png" width="41%" />
</p>

**Settings to sort, group and filter**

<p float="left">
  <img src="https://imgur.com/SGXlIbl.png" width="30%" />
  <img src="https://imgur.com/Wb2AmZl.png" width="25%" />
</p>


# Extra documentation

You can write extra documentation in markdown using `@lrd` in the PHPDoc on the `rules` method of the `Request` classes and on the controller methods.\
This will then be rendered as HTML on the dashboard.\
Documentation defined on the controller method is appended below documentation defined on the `rules` method.\
Example of using it in the controller:

```php
    /**
     * @lrd:start
     * # Hello markdown
     * Free `code` or *text* to write documentation in markdown
     * @lrd:end
     */
    public function index(MyIndexRequest $request): Resource
    {
```

# Extra parameters

You define extra parameters using `@LRDparam`.\
You can use `@LRDparam` in PHPDoc on both the `rules` methods and the controller methods.\
You can also overwrite rules using `@LRDparam`.
Meaning, when rules are defined in the `rules` method, you can overwrite those rules using `@LRDparam` in the PHPDoc above the `rules` method.
And you can overwrite those rules using `@LRDparam` in a PHPDoc on the controller methods.\
So, the precedence is `Controller method PHPDoc` < `Rules method PHPDoc` < `Rules method values`.

```php
    /**
     * @LRDparam username string|max:32
     * // either space or pipe
     * @LRDparam nickaname string|nullable|max:32
     * // override the default response codes
     * @LRDresponses 200|422
     */
    public function index(MyIndexRequest $request): Resource
    {
```

# Response codes
Without explicitly declaring response codes,
all routes are documented to return any of the response codes defined in the request-docs.php `default_responses` configuration.\
However, using `@LRDresponse 200|422` (spaces or pipes) within the PHPDoc on your controller methods,
you are able to explicitly define what status codes can be returned by the server.

# Configuration
Please view the `request-docs.php` config file to see how you can customise your experience.

# Testing

```bash
./vendor/bin/phpunit
```

# Linting

```bash
./vendor/bin/phpcs --standard=phpcs.xml --extensions=php --ignore=tests/migrations config/ src/
```

Fixing lints

```bash
./vendor/bin/php-cs-fixer fix src/
./vendor/bin/php-cs-fixer fix config/
```

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=rakutentech/laravel-request-docs&type=Date)](https://star-history.com/#rakutentech/laravel-request-docs&Date)

# Changelog

- Initial Release
- v1.9 Added improvements such as status code, response headers, custom request headers and fixed issues reported by users
- v1.10 Show PHP memory usage, gzip encoding fix
- v1.12 Bug fix of id, and Laravel 9 support
- v1.13 Laravel 9 support
- v1.15 Adds Filter and fall back to regexp upon Exception
- v1.17 Do not restrict to FormRequest
- v1.18 Fix where prism had fixed height. Allow the text area resize.
- v1.18 Updated UI and pushed unit tests
- v1.19 Exception -> Throwable for type error
- v1.20 Feature support open api 3.0.0 #10
- v1.21 Ability to add custom params
- v1.22 Boolean|File|Image support
- v1.22 Boolean|File|Image support
- v1.23 Bug fix for LRD doc block #76
- v1.27 A few fixes on width and added request_methods
- v2.0 UI Renewal to React Static
    - `@QAParam` is now `@LRDparam`
    - No special changes for users, upgrade to v2.x as usual
    - Upgrading users will need to republish config
- v2.1 UI - adds search bar and few alignment fixes on table
- v2.2 PHP 8.1 and 8.2 support added
       - Groupby enabled for routes and controllers
- v2.3 Bug fix for local storage (tabs) and full UI refactored after alpha
- v2.4 Show version on navbar and curl is using ace editor
- v2.5 Groupby final fix and local storage clear button. Other UI refactor
- v2.6 File uploads
- v2.7 Show activity on Eloquent models
- v2.8 Show full activity on Eloquent models
- v2.13 Bug fixes, and nested params support
- v2.14 Adds path params support
- v2.16 Top Navbar is fixed
- v2.19 Publish _astro assets
- v2.25 `laravel-request-docs:export`  command to export
- v2.28 Allow extra documentation to be defined on the `rules` method of the Request class. By @Ken-vdE
- v2.31 Customized title, vup js and PHP to latest. Customized headers. Save response history.
- v2.40 A few bug fixes.


# Contributors


<a href="https://github.com/rakutentech/laravel-request-docs/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=rakutentech/laravel-request-docs" />
</a>
