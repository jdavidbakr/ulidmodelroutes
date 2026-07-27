<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use jdavidbakr\UlidModelRoutes\HasUlidRouteKey;

beforeEach(function (): void {
    Schema::dropIfExists('posts');
    Schema::dropIfExists('teams');

    Schema::create('posts', function (Blueprint $table): void {
        $table->id();
        $table->ulid('ulid')->nullable()->unique();
        $table->string('title')->nullable();
        $table->timestamps();
    });

    Schema::create('teams', function (Blueprint $table): void {
        $table->id();
        $table->ulid('public_id')->nullable()->unique();
        $table->string('name')->nullable();
        $table->timestamps();
    });
});

it('assigns a ulid route key on create and resolves route binding by that key', function (): void {
    $post = new class extends Model
    {
        use HasUlidRouteKey;

        protected $table = 'posts';

        protected $guarded = [];
    };

    $post->forceFill(['title' => 'First post'])->save();

    $generatedUlid = $post->getAttribute('ulid');

    expect(is_string($generatedUlid))->toBeTrue();

    if (! is_string($generatedUlid)) {
        return;
    }

    expect($post->getRouteKeyName())->toBe('ulid')
        ->and($post->getRouteKey())->toBe($generatedUlid)
        ->and(Str::isUlid($generatedUlid))->toBeTrue()
        ->and($post->resolveRouteBinding($generatedUlid)?->getKey())->toBe($post->getKey());
});

it('preserves an explicit ulid route key value', function (): void {
    $post = new class extends Model
    {
        use HasUlidRouteKey;

        protected $table = 'posts';

        protected $guarded = [];
    };

    $ulid = (string) Str::ulid();

    $post->forceFill([
        'title' => 'Imported post',
        'ulid' => $ulid,
    ])->save();

    expect($post->getAttribute('ulid'))->toBe($ulid);
});

it('supports overriding the route key column per model', function (): void {
    $team = new class extends Model
    {
        use HasUlidRouteKey;

        protected $table = 'teams';

        protected $guarded = [];

        protected ?string $ulidRouteKeyColumnName = 'public_id';
    };

    $team->forceFill(['name' => 'Acme'])->save();

    $publicId = $team->getAttribute('public_id');

    expect(is_string($publicId))->toBeTrue();

    if (! is_string($publicId)) {
        return;
    }

    expect($team->getRouteKeyName())->toBe('public_id')
        ->and(Str::isUlid($publicId))->toBeTrue()
        ->and($team->resolveRouteBinding($publicId)?->getKey())->toBe($team->getKey());
});

it('falls back to the configured default column name', function (): void {
    config()->set('ulidmodelroutes.default_column_name', 'public_id');

    $team = new class extends Model
    {
        use HasUlidRouteKey;

        protected $table = 'teams';

        protected $guarded = [];
    };

    $team->forceFill(['name' => 'Configured'])->save();

    $publicId = $team->getAttribute('public_id');

    expect(is_string($publicId))->toBeTrue();

    if (! is_string($publicId)) {
        return;
    }

    expect($team->getRouteKeyName())->toBe('public_id')
        ->and(Str::isUlid($publicId))->toBeTrue();
});
