<?php

use Cerbero\LaravelEnum\BackedEnum;
use Cerbero\LaravelEnum\CasesCollection;

it('turns into a string', function() {
    expect((string) new CasesCollection(BackedEnum::cases()))->toBe('[1,2,3]');
});

it('turns into a JSON', function() {
    expect((new CasesCollection(BackedEnum::cases()))->toJson())->toBe('[1,2,3]');
});

it('is JSON serializable', function() {
    expect(json_encode(new CasesCollection(BackedEnum::cases())))->toBe('[1,2,3]');
});

it('retrieves the cast for the given enum', function() {
    expect(CasesCollection::of(BackedEnum::class))->toBe(CasesCollection::class . ':' . BackedEnum::class);
});
