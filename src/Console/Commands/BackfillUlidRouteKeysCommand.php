<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes\Console\Commands;

use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use jdavidbakr\UlidModelRoutes\HasUlidRouteKey;

class BackfillUlidRouteKeysCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'ulidmodelroutes:backfill
        {model : The Eloquent model class to backfill}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing ULID route keys for an Eloquent model.';

    public function handle(): int
    {
        $modelClass = $this->argument('model');

        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            $this->components->error('The provided model class could not be found.');

            return self::FAILURE;
        }

        if (! is_subclass_of($modelClass, Model::class)) {
            $this->components->error('The provided class must extend Illuminate\\Database\\Eloquent\\Model.');

            return self::FAILURE;
        }

        if (! in_array(HasUlidRouteKey::class, class_uses_recursive($modelClass), true)) {
            $this->components->error('The provided model must use the HasUlidRouteKey trait.');

            return self::FAILURE;
        }

        $prototype = new $modelClass();
        $routeKeyColumn = (string) $prototype->getRouteKeyName();
        $keyName = $prototype->getKeyName();
        $updated = 0;

        $prototype->newQuery()
            ->where(static function (EloquentBuilder $query) use ($routeKeyColumn): void {
                $query->whereNull($routeKeyColumn)
                    ->orWhere($routeKeyColumn, '');
            })
            ->orderBy($keyName)
            ->cursor()
            ->each(function (Model $model) use ($prototype, $routeKeyColumn, $keyName, &$updated): void {
                $ulid = (string) Str::ulid($this->resolveTimestamp($model));

                $prototype->getConnection()
                    ->table($prototype->getTable())
                    ->where($keyName, $model->getKey())
                    ->update([$routeKeyColumn => $ulid]);

                $updated++;
            });

        $this->components->info(sprintf(
            'Backfilled %d records on %s.%s.',
            $updated,
            $prototype->getTable(),
            $routeKeyColumn,
        ));

        return self::SUCCESS;
    }

    protected function resolveTimestamp(Model $model): ?DateTimeInterface
    {
        $createdAtColumn = $model->getCreatedAtColumn();

        if (! is_string($createdAtColumn) || $createdAtColumn === '') {
            return null;
        }

        $value = $model->getAttribute($createdAtColumn);

        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value);
        }

        return null;
    }
}
