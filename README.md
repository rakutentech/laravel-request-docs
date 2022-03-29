<p align="center">
  <a href="https://github.com/rakutentech/laravel-request-docs">
    <img alt="Laravel Request Docs" src="https://imgur.com/9eDTUaI.png" width="360">
  </a>
</p>

<p align="center">
  The Hassle-Free automatic API documentation generation for Laravel. <br>
  A Swagger alernative.
</p>

**Fast:** Install on any Laravel Project

**Hassle Free:** Auto Generate API Documentation for request rules and parameters

**Analyze:** In build SQL query time analyzer, response time and headers output.

## Features

- Automatic routes fetching from Laravel Routes
- Automatic rules fetching from injected Request
- Support for Authorization Headers
- Support for SQL query, response time and memory consumption by request on Laravel
- Intelligent auto request builder using ``faker.js``
- Display extra documentation using markdown
# Read on Medium

Automatically generate api documentation for Laravel without writing annotations.

Read more: https://medium.com/web-developer/laravel-automatically-generate-api-documentation-without-annotations-a-swagger-alternative-e0699409a59e

## Requirements

| Lang    | Version           |
| :------ | :---------------- |
| PHP     | 7.4 or 8.0        |
| Laravel | 6.* or 8.* or 9.* |

# Installation

You can install the package via composer:

```bash
composer require rakutentech/laravel-request-docs --dev
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag=request-docs-config
```

# Usage

View in the browser on ``/request-docs/``

or generate a static HTML

```php
php artisan lrd:generate
```

Docs HTML is generated inside ``docs/``.

# Design pattern

In order for this plugin to work, you need to follow the design pattern by injecting the request class inside the controller.
For extra documentation you can use markdown inside your controller method as well.

![Design pattern](https://imgur.com/yXjq3jp.png)

# Screenshots

**Generated API documentation**

![Preview](https://imgur.com/8DvBBhs.png)

**Try API**

![Preview](https://imgur.com/kcKVSzm.png)

**SQL query profile**

![Preview](https://imgur.com/y8jT3jj.png)

**Resonse profile**

![Preview](https://imgur.com/U0Je956.png)

**Customize Headers**

![Preview](https://imgur.com/5ydtRd8.png)


# Extra

You write extra documentation in markdown which will be rendered as HTML on the dashboard.
Example of using it in controller

```php
    /**
     * @lrd:start
     * #Hello markdown
     * ## Documentation for /my route
     * @lrd:end
     */
    public function index(MyIndexRequest $request): Resource
    {
```

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

# Changelog

- Initial Release
- v1.9 Added improvements such as status code, response headers, custom request headers and fixed issues reported by users
- v1.10 Show PHP memory usage, gzip encoding fix
- v1.12 Bug Fix of id, and Laravel 9 support
- v1.13 Laravel 9 support
- v1.15 Adds Filter and fall back to regexp upon Exception
- v1.17 Donot restrict to FormRequest
- v1.18 Fix where prism had fixed height. Allow text area resize.
- v1.18 Updated UI and pushed unit tests

