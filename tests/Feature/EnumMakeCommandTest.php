<?php

use Laravel\Prompts\Prompt;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\TextareaPrompt;
use Laravel\Prompts\TextPrompt;

it('succeeds if an enum already exists', function() {
    $this
        ->artisan('enum:make', ['enum' => 'App/Enums/Enum1'])
        ->expectsOutput('The enum App\Enums\Enum1 already exists.')
        ->assertExitCode(0);
});

it('fails if the backed option is not supported', function() {
    $this
        ->artisan('enum:make', ['enum' => 'App/Enums/Test', 'cases' => ['one'], '--backed' => 'foo'])
        ->expectsOutputToContain('The option --backed supports only')
        ->assertExitCode(1);
});

it('generates enums', function(string $enum, ?string $backed) {
    $command = $this->artisan('enum:make', [
        'enum' => $enum,
        'cases' => ['CaseOne', 'CaseTwo'],
        '--backed' => $backed
    ]);

    expect($command)->toGenerate($enum);
})->with([
    ['App\Enums\Generated1', 'bitwise'],
    ['Domain\Common\Enums\Generated2', 'snake'],
    ['SubDirectory\Generated3', null],
]);

// it('generates enums with prompts', function() {
//     // Currently textarea() of Laravel Prompts seems to have some testing issues.
//     // Apparently the method expectsQuestion() does not test textarea rightly.
//     // In alternative, we fallback the prompt with the expected user input.
//     // Prompt::fallbackWhen(true);
//     // TextPrompt::fallbackUsing(fn() => 'App\Enums\Generated1');
//     // SelectPrompt::fallbackUsing(fn() => 'bitwise');
//     // TextareaPrompt::fallbackUsing(fn() => 'CaseOne' . PHP_EOL . 'CaseTwo');

//     // $command = $this->artisan('enum:make')
//     //     ->expectsQuestion('The namespace of the enum', 'App\Enums\Generated1')
//     //     ->expectsQuestion('How cases should be backed', 'bitwise')
//     //     ->expectsQuestion('The cases (one per line)', 'CaseOne' . PHP_EOL . 'CaseTwo');

//     expect($this->artisan('enum:make'))->toGenerate('App\Enums\Generated1');
// })->only();
