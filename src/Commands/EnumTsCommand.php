<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Commands;

use Cerbero\LaravelEnum\Enums;
use Cerbero\LaravelEnum\Services\TypeScript;
use Illuminate\Console\Command;

use function Cerbero\Enum\normalizeEnums;
use function Cerbero\LaravelEnum\output;
use function Laravel\Prompts\multiselect;

/**
 * The console command to synchronize enums in TypeScript.
 */
final class EnumTsCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize enums in TypeScript';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enum:ts
                            {enums?* : The enums to synchronize}
                            {--a|all : Whether all enums should be synchronized}
                            {--f|force : Whether existing enums should be overwritten}';

    /**
     * Handle the command.
     */
    public function handle(): int
    {
        if (! $enums = $this->enums()) {
            $this->info('No enums to annotate.');

            return self::SUCCESS;
        }

        $succeeded = true;
        $force = !! $this->option('force');

        foreach($enums as $enum) {
            $succeeded = output($this->output, $enum, fn() => (new TypeScript($enum))->sync($force)) && $succeeded;
        }

        return $succeeded ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Retrieve the enums to annotate.
     *
     * @return list<class-string<\UnitEnum>>
     */
    private function enums(): array
    {
        /** @var list<class-string<\UnitEnum>> */
        return match (true) {
            ! empty($enums = (array) $this->argument('enums')) => normalizeEnums($enums),
            empty($enums = [...Enums::namespaces()]) => [],
            $this->option('all') => $enums,
            /** @phpstan-ignore argument.type */
            default => multiselect('Enums to synchronize:', $enums, required: true, hint: 'Press space to select.'),
        };
    }
}
