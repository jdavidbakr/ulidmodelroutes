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

- apply the `jdavidbakr/ulidmodelroutes` package so Eloquent models keep integer primary keys while exposing ULIDs in routes

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

### 4. Validate the integration

- confirm new records receive a ULID automatically
- confirm `route()` generates URLs with the ULID instead of the integer primary key
- confirm implicit route model binding resolves `{model}` parameters by the ULID column

### 5. Backfill existing records when adopting the package

- run `php artisan ulidmodelroutes:backfill "App\\Models\\Post"` after adding the nullable ULID column to an existing table
- expect the command to fill only missing route key values
- expect the command to derive the ULID timestamp from `created_at` when that value exists, which helps preserve the original sort order

## Rules, References, and Templates

Read before executing:

- the package only provides a trait and config; it does not publish migrations, views, routes, translations, or assets
- the target table must already contain the ULID column used for route binding
- keep the ULID column unique so route binding remains deterministic
- the backfill command does not add the column for the application; run it only after the schema change exists

## Examples

- a `Post` model keeps its integer `id` primary key, adds a unique `ulid` column, uses `HasUlidRouteKey`, and resolves `/posts/{post}` by ULID
- a `Team` model uses `protected ?string $ulidRouteKeyColumnName = 'public_id';` so routes resolve `/teams/{team}` by `public_id`
- an existing `Order` table adds a nullable `ulid` column, runs `php artisan ulidmodelroutes:backfill "App\\Models\\Order"`, then adds the unique constraint after values are populated

## Anti-patterns

- do not assume the package creates the ULID column for the application
- do not change the model primary key to the ULID unless the application explicitly wants that architecture
- do not document package internals here; keep the skill focused on adoption in Laravel apps
