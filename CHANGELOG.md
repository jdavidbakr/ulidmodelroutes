# Release Notes

## [Unreleased](https://github.com/jdavidbakr/ulidmodelroutes/compare/1.0...HEAD)

## [1.0](https://github.com/jdavidbakr/ulidmodelroutes/compare/v1.0.0...1.0) - 2026-07-28

1.0

## [v1.0.0](https://github.com/jdavidbakr/ulidmodelroutes/compare/v0.1.0...v1.0.0) - 2026-07-28

First stable release.

### Added

- `HasUlidRouteKey` trait for automatic route key generation on model creation.
- Configurable route key generation strategy with `id_type` (`ulid` or `uuid`) and `uuid_type` (`uuid7`, `uuid4`, or `ordered`).
- `ulidmodelroutes:backfill` Artisan command to backfill missing route keys on existing records.
- Backfill timestamp support using `created_at` for ULID and UUIDv7 generation to preserve sort order characteristics when possible.
- Feature tests covering trait behavior, custom route key columns, UUID strategy selection, and backfill behavior.

### Changed

- Package service provider and documentation now reflect the shipped public API.
- README and Boost skill documentation updated with installation, configuration, backfill workflow, and UUID/ULID guidance.

## [v0.1.0](https://github.com/jdavidbakr/ulidmodelroutes/compare/...v0.1.0) - 202x-xx-xx

Initial pre-release.
