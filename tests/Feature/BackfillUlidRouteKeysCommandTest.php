<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use jdavidbakr\UlidModelRoutes\HasUlidRouteKey;
use Symfony\Component\Uid\Ulid;

class BackfillPost extends Model
{
    use HasUlidRouteKey;

    protected $table = 'backfill_posts';

    protected $guarded = [];
}

class BackfillTeam extends Model
{
    use HasUlidRouteKey;

    protected $table = 'backfill_teams';

    protected $guarded = [];

    protected ?string $ulidRouteKeyColumnName = 'public_id';
}

function toImmutableDate(mixed $value): ?CarbonImmutable
{
    if ($value instanceof DateTimeInterface) {
        return CarbonImmutable::instance($value);
    }

    if (is_string($value) && $value !== '') {
        return CarbonImmutable::parse($value);
    }

    return null;
}

function toUlidString(mixed $value): ?string
{
    if (is_string($value) && $value !== '') {
        return $value;
    }

    if (is_object($value) && method_exists($value, '__toString')) {
        $stringValue = (string) $value;

        return $stringValue !== '' ? $stringValue : null;
    }

    return null;
}

beforeEach(function (): void {
    Schema::dropIfExists('backfill_posts');
    Schema::dropIfExists('backfill_teams');

    Schema::create('backfill_posts', function (Blueprint $table): void {
        $table->id();
        $table->ulid('ulid')->nullable()->unique();
        $table->string('title')->nullable();
        $table->timestamp('created_at', 3)->nullable();
        $table->timestamp('updated_at', 3)->nullable();
    });

    Schema::create('backfill_teams', function (Blueprint $table): void {
        $table->id();
        $table->ulid('public_id')->nullable()->unique();
        $table->string('name')->nullable();
        $table->timestamp('created_at', 3)->nullable();
        $table->timestamp('updated_at', 3)->nullable();
    });
});

it('backfills missing ulids from created_at without overwriting existing values', function (): void {
    $firstCreatedAt = CarbonImmutable::parse('2024-01-10 12:30:45.123 UTC');
    $secondCreatedAt = CarbonImmutable::parse('2024-01-11 13:31:46.456 UTC');
    $existingUlid = (string) Str::ulid(CarbonImmutable::parse('2024-01-12 14:32:47.789 UTC'));

    DB::table('backfill_posts')->insert([
        'title' => 'first',
        'ulid' => null,
        'created_at' => $firstCreatedAt,
        'updated_at' => $firstCreatedAt,
    ]);

    DB::table('backfill_posts')->insert([
        'title' => 'second',
        'ulid' => null,
        'created_at' => $secondCreatedAt,
        'updated_at' => $secondCreatedAt,
    ]);

    DB::table('backfill_posts')->insert([
        'title' => 'existing',
        'ulid' => $existingUlid,
        'created_at' => $secondCreatedAt,
        'updated_at' => $secondCreatedAt,
    ]);

    $first = BackfillPost::query()->where('title', 'first')->firstOrFail();
    $second = BackfillPost::query()->where('title', 'second')->firstOrFail();
    $existing = BackfillPost::query()->where('title', 'existing')->firstOrFail();

    $exitCode = Artisan::call('ulidmodelroutes:backfill', ['model' => BackfillPost::class]);

    expect($exitCode)->toBe(0)
        ->and(Artisan::output())->toContain('Backfilled 2 records on backfill_posts.ulid.');

    $first->refresh();
    $second->refresh();
    $existing->refresh();

    $firstUlid = toUlidString($first->getAttribute('ulid'));
    $secondUlid = toUlidString($second->getAttribute('ulid'));
    $persistedExistingUlid = $existing->getAttribute('ulid');
    $firstPersistedCreatedAt = toImmutableDate($first->getAttribute('created_at'));
    $secondPersistedCreatedAt = toImmutableDate($second->getAttribute('created_at'));

    expect($firstUlid)->not()->toBeNull()
        ->and($secondUlid)->not()->toBeNull()
        ->and($persistedExistingUlid)->toBe($existingUlid)
        ->and($firstPersistedCreatedAt instanceof CarbonImmutable)->toBeTrue()
        ->and($secondPersistedCreatedAt instanceof CarbonImmutable)->toBeTrue();

    if ($firstUlid === null || $secondUlid === null || ! $firstPersistedCreatedAt instanceof CarbonImmutable || ! $secondPersistedCreatedAt instanceof CarbonImmutable) {
        return;
    }

    expect(Ulid::fromString($firstUlid)->getDateTime()->format('Uv'))->toBe($firstPersistedCreatedAt->format('Uv'))
        ->and(Ulid::fromString($secondUlid)->getDateTime()->format('Uv'))->toBe($secondPersistedCreatedAt->format('Uv'))
        ->and(strcmp($firstUlid, $secondUlid))->toBeLessThan(0);
});

it('uses the model route key column when backfilling custom columns', function (): void {
    $createdAt = CarbonImmutable::parse('2024-02-01 08:15:30.250 UTC');

    DB::table('backfill_teams')->insert([
        'name' => 'Acme',
        'public_id' => null,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    $team = BackfillTeam::query()->where('name', 'Acme')->firstOrFail();

    $exitCode = Artisan::call('ulidmodelroutes:backfill', ['model' => BackfillTeam::class]);

    expect($exitCode)->toBe(0)
        ->and(Artisan::output())->toContain('Backfilled 1 records on backfill_teams.public_id.');

    $team->refresh();

    $publicId = toUlidString($team->getAttribute('public_id'));
    $persistedCreatedAt = toImmutableDate($team->getAttribute('created_at'));

    expect($publicId)->not()->toBeNull()
        ->and($persistedCreatedAt instanceof CarbonImmutable)->toBeTrue();

    if ($publicId === null || ! $persistedCreatedAt instanceof CarbonImmutable) {
        return;
    }

    expect(Ulid::fromString($publicId)->getDateTime()->format('Uv'))->toBe($persistedCreatedAt->format('Uv'));
});
