<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes;

use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-require-extends Model
 */
trait HasUlidRouteKey
{
    /**
     * Boot the trait and ensure the model has a route key identifier.
     */
    protected static function bootHasUlidRouteKey(): void
    {
        $modelClass = static::class;

        forward_static_call([$modelClass, 'creating'], static function (Model $model): void {
            if (empty($model->getAttribute($model->getRouteKeyName()))) {
                $model->setAttribute($model->getRouteKeyName(), RouteKeyGenerator::generate());
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        if (property_exists($this, 'ulidRouteKeyColumnName')) {
            $columnName = $this->ulidRouteKeyColumnName;

            if (is_string($columnName) && $columnName !== '') {
                return $columnName;
            }
        }

        return config('ulidmodelroutes.default_column_name', 'ulid');
    }
}
