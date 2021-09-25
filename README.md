# Laravel Docs Generator

Automatically generate api documentation for Laravel without writing annotations.

Read more: https://medium.com/web-developer/laravel-automatically-generate-api-documentation-without-annotations-a-swagger-alternative-e0699409a59e

## Requirements

| Lang    | Version    |
| :------ | :--------- |
| PHP     | 7.4 or 8.0 |
| Laravel | 6.* or 8.* |

## Installation

You can install the package via composer:

```bash
composer require rakutentech/laravel-request-docs --dev
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag=request-docs-config
```

## Usage

View in the browser on ``/request-docs/``

or generate a static HTML

```php
php artisan lrd:generate
```

Docs HTML is generated inside ``docs/``.

## Design pattern

In order for this plugin to work, you need to follow the design pattern by injecting the request class inside the controller.
For extra documentation you can use markdown inside your controller method as well.

![Design pattern](https://imgur.com/yXjq3jp.png)

### Screenshots

Generated API documentation

![Preview](https://imgur.com/8DvBBhs.png)

Try API

![Preview](https://imgur.com/kcKVSzm.png)


## Testing

```bash
./vendor/bin/phpunit
```

## Changelog

- Initial Release

