<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes;

use DateTimeInterface;
use Illuminate\Support\Str;

class RouteKeyGenerator
{
    public static function generate(?DateTimeInterface $time = null): string
    {
        $idType = strtolower((string) config('ulidmodelroutes.id_type', 'ulid'));

        if ($idType === 'uuid') {
            return self::generateUuid($time);
        }

        return (string) Str::ulid($time);
    }

    protected static function generateUuid(?DateTimeInterface $time = null): string
    {
        $uuidType = strtolower((string) config('ulidmodelroutes.uuid_type', 'uuid7'));

        return match ($uuidType) {
            'uuid4', 'v4', '4' => (string) Str::uuid(),
            'ordered' => (string) Str::orderedUuid(),
            default => (string) Str::uuid7($time),
        };
    }
}
