<?php

use Cerbero\LaravelEnum\BackedEnum;
use Cerbero\LaravelEnum\CasesCollection;

it('collects its cases', function() {
    expect(BackedEnum::collect())->toBeInstanceOf(CasesCollection::class);
});
