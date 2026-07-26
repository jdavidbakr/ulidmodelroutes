<div align="center">
    <h1>Ulidmodelroutes</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/jdavidbakr/ulidmodelroutes"><img src="https://img.shields.io/packagist/v/jdavidbakr/ulidmodelroutes.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/jdavidbakr/ulidmodelroutes"><img src="https://img.shields.io/packagist/php-v/jdavidbakr/ulidmodelroutes.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/jdavidbakr/ulidmodelroutes"><img src="https://badge.laravel.cloud/badge/jdavidbakr/ulidmodelroutes?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/jdavidbakr/ulidmodelroutes/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/jdavidbakr/ulidmodelroutes/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/jdavidbakr/ulidmodelroutes"><img src="https://img.shields.io/packagist/dt/jdavidbakr/ulidmodelroutes.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Configures models to have ULID route keys while still having integer primary keys

## Installation

You can install the package via Composer:

```bash
composer require jdavidbakr/ulidmodelroutes
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="ulidmodelroutes"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="ulidmodelroutes-config"
```

### Publishing and Running the Migrations

```bash
php artisan vendor:publish --tag="ulidmodelroutes-migrations"
php artisan migrate
```

### Publishing the Views

```bash
php artisan vendor:publish --tag="ulidmodelroutes-views"
```

### Publishing the Translations

```bash
php artisan vendor:publish --tag="ulidmodelroutes-lang"
```

### Publishing the Public Assets

```bash
php artisan vendor:publish --tag="ulidmodelroutes-assets"
```

## Usage

<!-- Add a basic usage example here. -->

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Ulidmodelroutes! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [J David Baker](https://github.com/jdavidbakr)
- [All Contributors](../../contributors)

## License

Ulidmodelroutes is open-sourced software licensed under the [MIT license](LICENSE.md).
