<?php

use App\Enums\Enum1;
use App\Enums\Enum2;
use Cerbero\LaravelEnum\Enums;

it('warns that no enums were annotated if invalid enums are provided', function() {
    $this
        ->artisan('enum:ts', ['enums' => ['InvalidEnum']])
        ->expectsOutput('No enums to annotate.')
        ->assertExitCode(0);
});

it('warns that no enums were annotated if no enums can be found', function() {
    Enums::setPaths('noEnums');

    $this
        ->artisan('enum:ts', ['--all' => true])
        ->expectsOutput('No enums to annotate.')
        ->assertExitCode(0);

    Enums::setPaths('app/Enums');
});

it('synchronizes all the discoverable enums', function() {
    expect($this->artisan('enum:ts', ['--all' => true]))->toTypeScript([
        Enum1::class,
        Enum2::class,
    ]);
});
