<?php

use Cerbero\LaravelEnum\Enums;

it('yields the namespace of enums in the default path', function() {
    expect(Enums::namespaces())->sequence(
        'App\Enums\Enum1',
        'App\Enums\Enum2',
    );
});

it('yields the namespace of enums in custom paths', function() {
    Enums::paths('app/Enums', 'domain/*/Enums');

    expect(Enums::namespaces())->sequence(
        'App\Enums\Enum1',
        'App\Enums\Enum2',
        'Domain\Common\Enums\CacheKeys',
        'Domain\Common\Enums\SessionKeys',
        'Domain\Payouts\Enums\PayoutStatuses',
    );

    // reset the initial state
    Enums::paths('app/Enums');
});
