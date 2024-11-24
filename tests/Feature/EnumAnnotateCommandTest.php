<?php

use App\Enums\Enum1;
use App\Enums\Enum2;
use Cerbero\LaravelEnum\Enums;
use Domain\Common\Enums\CacheKeys;
use Domain\Common\Enums\SessionKeys;
use Domain\Payouts\Enums\PayoutStatuses;
use Unloaded\NoTrait;

it('warns that no enums were annotated if invalid enums are provided', function() {
    $this
        ->artisan('enum:annotate', ['enums' => ['InvalidEnum']])
        ->expectsOutput('No enums to annotate.')
        ->assertExitCode(0);
});

it('warns that no enums were annotated if no enums can be found', function() {
    Enums::paths('noEnums');

    $this
        ->artisan('enum:annotate', ['--all' => true])
        ->expectsOutput('No enums to annotate.')
        ->assertExitCode(0);

    Enums::paths('app/Enums'); // reset default path
});

it('displays an error message when it fails', function() {
    $this
        ->artisan('enum:annotate', ['enums' => [NoTrait::class]])
        ->expectsOutputToContain('The enum Unloaded\NoTrait must use the trait Cerbero\LaravelEnum\Concerns\Enumerates')
        ->assertExitCode(1);
});

it('annotates all the discoverable enums', function() {
    Enums::paths('app/Enums', 'domain/*/Enums');

    expect($this->artisan('enum:annotate', ['--all' => true]))->toAnnotate([
        Enum1::class,
        Enum2::class,
        CacheKeys::class,
        SessionKeys::class,
        PayoutStatuses::class,
    ]);

    Enums::paths('app/Enums'); // reset default path
});
