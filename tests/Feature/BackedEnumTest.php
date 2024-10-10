<?php

use Cerbero\LaravelEnum\BackedEnum;
use Cerbero\LaravelEnum\Enums;

it('translates when a meta attribute is not found', function() {
    expect(BackedEnum::One->description(['ciao' => 'bye']))->toBe('My description');
});

it('translates with placeholders', function() {
    expect(BackedEnum::One->dynamic(['value' => 123]))->toBe('The value is 123');
});

it('translates from custom keys', function() {
    Enums::translateFrom(fn(UnitEnum $case) => 'custom.' . $case->name);

    expect(BackedEnum::One->description())->toBe('My custom description');

    (fn() => self::$translateFrom = null)->bindTo(null, Enums::class)();
});

it('fails if a translation cannot be found', fn() => BackedEnum::One->unknownTranslation())
    ->throws(ValueError::class, '"unknownTranslation" is not a valid meta for enum "Cerbero\LaravelEnum\BackedEnum"');
