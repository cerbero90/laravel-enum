<?php

use Cerbero\LaravelEnum\BackedEnum;
use Cerbero\LaravelEnum\Enums;

it('translates when a meta attribute is not found', function() {
    expect(BackedEnum::One->description(ciao: 'bye'))->toBe('My description');
});

it('translates with placeholders', function() {
    expect(BackedEnum::One->dynamic(value: 123))->toBe('The value is 123');
});

it('translates from custom keys', function() {
    Enums::translateFrom(fn(UnitEnum $case, string $method) => "custom.{$case->name}.{$method}");

    expect(BackedEnum::One->description())->toBe('My custom description');

    (fn() => self::$translateFrom = null)->bindTo(null, Enums::class)();
});

it('fails if a translation cannot be found', fn() => BackedEnum::One->unknownTranslation())
    ->throws(ValueError::class, '"unknownTranslation" is not a valid meta for enum "Cerbero\LaravelEnum\BackedEnum"');

it('handles the call to an inaccessible enum method', function() {
    expect(BackedEnum::One())->toBe(1);
});

it('runs custom logic when calling an inaccessible case method', function() {
    Enums::onCall(function(object $case, string $name, array $arguments) {
        expect($case)->toBeInstanceOf(BackedEnum::class)
            ->and($name)->toBe('unknownMethod')
            ->and($arguments)->toBe([1, 2, 3]);

        return 'ciao';
    });

    expect(BackedEnum::One->unknownMethod(1, 2, 3))->toBe('ciao');

    (fn() => self::$onCall = null)->bindTo(null, Enums::class)();
});

it('handles the invocation of a case.', function() {
    expect((BackedEnum::One)())->toBe(1);
});

it('autowires a default meta', function() {
    expect(BackedEnum::One->handle(1, 2))
        ->toBe(3)
        ->and(BackedEnum::Three->handle(1, 2))
        ->toBe(3);
});

it('autowires a meta', function() {
    expect(BackedEnum::Two->handle(1, 2))->toBe(123);
});
