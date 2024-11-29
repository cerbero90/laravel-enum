<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Commands;

use Cerbero\LaravelEnum\Enums;
use Cerbero\LaravelEnum\Services\Annotator;
use Illuminate\Console\Command;
use Throwable;

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
    public function handle(Annotator $annotator): int
    {
        if (! $enums = $this->enums()) {
            $this->info('No enums to annotate.');

            return self::SUCCESS;
        }

        $e = null;
        $succeeded = true;

        foreach($enums as $enum) {
            try {
                $succeeded = $annotator->annotate($enum, !! $this->option('force')) && $succeeded;
            } catch (Throwable $e) {
                $succeeded = false;
            }

            $message = $succeeded
                ? "<bg=#a3e635;fg=#3f6212;options=bold> DONE </> {$enum}\n"
                : "<bg=#f87171;fg=#991b1b;options=bold> FAIL </> {$enum} <fg=#dc2626>{$e?->getMessage()}</>\n";

            $this->line($message);
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
        if ($enums = (array) $this->argument('enums')) {
            /** @var list<string> $enums */
            $namespaces = array_map(fn(string $enum) => strtr($enum, '/', '\\'), $enums);

            /** @var list<class-string<\UnitEnum>> */
            return array_unique(array_filter($namespaces, 'enum_exists'));
        }

        /** @var list<class-string<\UnitEnum>> */
        return match (true) {
            empty($enums = [...Enums::namespaces()]) => [],
            $this->option('all') => $enums,
            default => multiselect('Enums to annotate:', $enums, required: true, hint: 'Press space to select.'),
        };
    }
}
