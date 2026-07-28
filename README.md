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

Configures models to have ULID or UUID route keys while still having integer primary keys

## Installation

You can install the package via Composer:

```bash
composer require jdavidbakr/ulidmodelroutes
```

If you want to change the default ULID column name, publish the config file:

```bash
php artisan vendor:publish --tag="ulidmodelroutes-config"
```

## Usage

Add an identifier column to each model table that should be used for route model binding. Keep it indexed and unique.

For ULIDs:

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('posts', function (Blueprint $table) {
    $table->ulid('ulid')->nullable()->unique();
});
```

For UUIDs:

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('posts', function (Blueprint $table) {
    $table->uuid('uuid')->nullable()->unique();
});
```

Apply the trait to the model.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use jdavidbakr\UlidModelRoutes\HasUlidRouteKey;

class Post extends Model
{
    use HasUlidRouteKey;
}
```

Once the trait is applied:

- new models receive a route identifier automatically when they are created
- URL generation uses the configured route identifier instead of the integer primary key
- implicit route model binding resolves records by the configured route key column

Example route:

```php
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/posts/{post}', function (Post $post) {
    return $post;
});
```

Example URL generation:

```php
$post = Post::query()->first();

route('posts.show', $post);
// /posts/01K10R0X6T0B3R3D4Y8S0KQ7M4
```

### Customizing the column name per model

If a model should use a different route key column, define `ulidRouteKeyColumnName` on that model.

```php
class Team extends Model
{
    use HasUlidRouteKey;

    protected ?string $ulidRouteKeyColumnName = 'public_id';
}
```

### Setting a package-wide default column

After publishing the config file, change the default column name:

```php
return [
    'default_column_name' => 'public_id',
];
```

### Switching between ULID and UUID

By default, the package generates ULIDs.

You can switch to UUID generation in the config file:

```php
return [
    'id_type' => 'uuid', // 'ulid' or 'uuid'
    'uuid_type' => 'uuid7', // 'uuid7', 'uuid4', or 'ordered'
];
```

Notes:

- `ulid` keeps lexicographic ordering and supports `created_at`-based backfill timestamps.
- `uuid7` is time-ordered and also supports `created_at`-based backfill timestamps.
- `uuid4` is random and does not preserve chronological sort order.

### Existing data

This package does not create database columns for you. If you are adding route identifiers to an existing table, add the column in a migration and then backfill missing values before making the column unique.

You can backfill an existing model with the included Artisan command:

```bash
php artisan ulidmodelroutes:backfill "App\\Models\\Post"
```

The command only fills missing route key values. When a row has a `created_at` value, the generated ULID or UUIDv7 uses that timestamp so the resulting identifiers stay as close as possible to the model's original creation order.

Recommended rollout for existing tables:

1. Add the nullable ULID column.
2. Run the backfill command for the model.
3. Verify all rows have values.
4. Add the unique index and, if desired, make the column non-nullable.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to UlidModelRoutes.

## Security Vulnerabilities

Please open a private security advisory or contact the maintainer directly if you discover a vulnerability.

## Credits

- [J David Baker](https://github.com/jdavidbakr)

## License

Ulidmodelroutes is open-sourced software licensed under the [MIT license](LICENSE.md).
