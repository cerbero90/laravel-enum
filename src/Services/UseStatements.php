<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

use function Cerbero\LaravelEnum\yieldLines;

/**
 * The use statements collector.
 *
 * @implements IteratorAggregate<string, class-string>
 */
final class UseStatements implements IteratorAggregate
{
    /**
     * The regular expression to extract the use statements already present on the enum.
     */
    public const RE_STATEMENT = '~^use\s+([^\s;]+)(?:\s+as\s+([^;]+))?~i';

    /**
     * Instantiate the class.
     *
     * @param Inspector<\UnitEnum> $inspector
     */
    public function __construct(private readonly Inspector $inspector) {}

    /**
     * Retrieve the use statements.
     *
     * @return ArrayIterator<string, class-string>
     */
    public function getIterator(): Traversable
    {
        $useStatements = [
            ...$this->fromMethodAnnotations(),
            ...$this->existing(),
        ];

        asort($useStatements);

        return new ArrayIterator($useStatements);
    }

    /**
     * Retrieve the use statements from the method annotations.
     *
     * @return array<string, class-string>
     */
    public function fromMethodAnnotations(): array
    {
        $useStatements = [];

        foreach ($this->inspector->methodAnnotations() as $annotation) {
            foreach ($annotation->namespaces as $namespace) {
                if (! $this->inspector->hasSameNamespace($namespace)) {
                    $useStatements[class_basename($namespace)] = $namespace;
                }
            }
        }

        return $useStatements;
    }

    /**
     * Retrieve the use statements already present on the enum.
     *
     * @return array<string, class-string>
     */
    public function existing(): array
    {
        $useStatements = [];

        foreach (yieldLines($this->inspector->filename()) as $line) {
            if (strpos($line, 'enum') === 0) {
                break;
            }

            if (preg_match(self::RE_STATEMENT, $line, $matches)) {
                $useStatements[$matches[2] ?? class_basename($matches[1])] = $matches[1];
            }
        }

        /** @var array<string, class-string> */
        return $useStatements;
    }
}
