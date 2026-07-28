---
name: ulidmodelroutes-development
description: >
  Configure and apply the Ulidmodelroutes package in Laravel applications.
license: MIT
metadata:
  author: J David Baker
---

# Ulidmodelroutes

Use this skill when a Laravel application needs to integrate the Ulidmodelroutes package.

## Primary Goal

- apply the `jdavidbakr/ulidmodelroutes` package so Eloquent models keep integer primary keys while exposing ULID or UUID route identifiers

## Workflow

### 1. Inspect the Laravel app context

- confirm the app is a Laravel project
- identify which models should use ULID route keys
- inspect the related migrations to confirm each table has a unique ULID column

### 2. Apply the package's public API

- install the package with `composer require jdavidbakr/ulidmodelroutes`
- add `use HasUlidRouteKey;` to each model that should resolve routes by ULID
- leave the model primary key unchanged when the app still uses integer IDs internally
- rely on Laravel implicit route model binding once the trait is present

### 3. Configure the ULID column name when needed

- use the default `ulid` column name unless the app already has a different convention
- publish the config with `php artisan vendor:publish --tag="ulidmodelroutes-config"` when the app needs a package-wide default column name
- define `protected ?string $ulidRouteKeyColumnName = 'public_id';` on a specific model when only that model needs a different column

### 4. Choose the identifier type

- leave `id_type` as `ulid` for the default behavior
- set `id_type` to `uuid` when the app standard is UUID
- use `uuid_type` to select `uuid7`, `uuid4`, or `ordered`
- when choosing UUIDs, ensure the database column type and length match UUID values

### 5. Validate the integration

- confirm new records receive identifiers automatically
- confirm `route()` generates URLs with the configured identifier instead of the integer primary key
- confirm implicit route model binding resolves `{model}` parameters by the configured route key column

### 6. Backfill existing records when adopting the package

- run `php artisan ulidmodelroutes:backfill "App\\Models\\Post"` after adding the nullable ULID column to an existing table
- expect the command to fill only missing route key values
- expect the command to derive the timestamp from `created_at` when using ULID or UUIDv7, which helps preserve the original sort order

## Rules, References, and Templates

Read before executing:

- the package only provides a trait and config; it does not publish migrations, views, routes, translations, or assets
- the target table must already contain the configured route key column used for route binding
- keep the route key column unique so route binding remains deterministic
- the backfill command does not add the column for the application; run it only after the schema change exists

## Examples

- a `Post` model keeps its integer `id` primary key, adds a unique `ulid` column, uses `HasUlidRouteKey`, and resolves `/posts/{post}` by ULID
- a `Team` model uses `protected ?string $ulidRouteKeyColumnName = 'public_id';` so routes resolve `/teams/{team}` by `public_id`
- an existing `Order` table adds a nullable `ulid` column, runs `php artisan ulidmodelroutes:backfill "App\\Models\\Order"`, then adds the unique constraint after values are populated
- an app with UUID conventions sets `id_type` to `uuid`, `uuid_type` to `uuid7`, and uses a nullable `uuid` column for route keys

## Anti-patterns

- do not assume the package creates the ULID column for the application
- do not change the model primary key to the ULID unless the application explicitly wants that architecture
- do not document package internals here; keep the skill focused on adoption in Laravel apps
