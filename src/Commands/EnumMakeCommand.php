<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Commands;

use Cerbero\Enum\Enums\Backed;
use Cerbero\LaravelEnum\Services\Generator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;

use function Cerbero\LaravelEnum\output;
use function Cerbero\LaravelEnum\runAnnotate;
use function Cerbero\LaravelEnum\runTs;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

/**
 * The console command to create enums.
 */
final class EnumMakeCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enum:make
                            {enum? : The namespace of the enum}
                            {cases?* : The names of the enum cases}
                            {--b|backed= : How cases should be backed}
                            {--f|force : Whether the existing enum should be overwritten}
                            {--t|typescript : Whether the TypeScript enum should be created too}';

    /**
     * Handle the command.
     */
    public function handle(): int
    {
        $enum = $this->enum();
        $force = !! $this->option('force');

        if (enum_exists($enum) && ! $force) {
            $this->info("The enum {$enum} already exists.");

            return self::SUCCESS;
        }

        if (! $backed = $this->backed()) {
            $this->error('The option --backed supports only ' . implode(', ', Backed::names()));

            return self::FAILURE;
        }

        $generator = new Generator($enum, $this->cases($backed), $backed);
        $typeScript = !! $this->option('typescript');

        $succeeded = output($this->output, $enum, function () use ($generator, $enum, $force, $typeScript) {
            return $generator->generate($force)
                && runAnnotate($enum, $force)
                && ($typeScript ? runTs($enum, $force) : true);
        });

        return $succeeded ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Retrieve the enum namespace.
     *
     * @return class-string<\UnitEnum>
     */
    private function enum(): string
    {
        /** @var string $raw */
        $raw = $this->argument('enum') ?: text('The namespace of the enum', 'App\Enums\Permissions', required: true);

        /** @var class-string<\UnitEnum> */
        return strtr($raw, '/', '\\');
    }

    /**
     * Retrieve the selected backed case, if any.
     *
     * @throws InvalidArgumentException
     */
    private function backed(): ?Backed
    {
        if ($this->argument('enum') === null) {
            /** @phpstan-ignore argument.templateType */
            $options = Backed::pluck('label', 'name');
            /** @var array<string, string> $options */
            $name = select('How cases should be backed', $options);

            /** @var string $name */
            return Backed::from($name);
        }

        if (is_null($name = $this->option('backed'))) {
            return Backed::pure;
        }

        /** @var string $name */
        return Backed::tryFrom($name);
    }

    /**
     * Retrieve the cases, optionally backed.
     *
     * @return string[]
     */
    private function cases(Backed $backed): array
    {
        $placeholder = $backed->is(Backed::custom) ? "Case1=value1\nCase2=value2" : "Case1\nCase2";

        /** @var list<string> */
        return $this->argument('cases')
            ?: explode(PHP_EOL, trim(textarea('The cases (one per line)', $placeholder, required: true)));
    }
}
