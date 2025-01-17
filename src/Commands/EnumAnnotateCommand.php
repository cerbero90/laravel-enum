<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Commands;

use Cerbero\LaravelEnum\Enums;
use Cerbero\LaravelEnum\Services\Annotator;
use Illuminate\Console\Command;

use function Cerbero\Enum\normalizeEnums;
use function Cerbero\LaravelEnum\output;
use function Laravel\Prompts\multiselect;

/**
 * The console command to annotate enums.
 */
final class EnumAnnotateCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Annotate enums to ease IDE autocompletion';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enum:annotate
                            {enums?* : The enums to annotate}
                            {--a|all : Whether all enums should be annotated}
                            {--f|force : Whether existing annotations should be overwritten}';

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
            $succeeded = output($this->output, $enum, fn() => (new Annotator($enum))->annotate($force)) && $succeeded;
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
            default => multiselect('Enums to annotate:', $enums, required: true, hint: 'Press space to select.'),
        };
    }
}
