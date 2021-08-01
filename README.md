# Laravel Docs Generator

Automatically generate docs from request Rules


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
php artisan vendor:publish --provider="Rakutentech\LaravelERD\LaravelERDServiceProvider"
```

## Usage

```php
php artisan erd:generate
```

ERD HTML is generated inside ``docs/``.

### Sample

#### Screenshot


#### Get JSON output

## Testing

```bash
./vendor/bin/phpunit
```

## Changelog

- Initial Release - POC


## TODO

- Support Swagger
