<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\Enum\Services\UseStatements as BaseUseStatements;

/**
 * The use statements collector.
 *
 * @property-read Inspector $inspector
 */
final class UseStatements extends BaseUseStatements
{
    /**
     * Retrieve all the use statements.
     *
     * @return array<string, class-string>
     */
    public function all(): array
    {
        return [
            ...$this->fromMethodAnnotations(),
            ...$this->existing(),
        ];
    }

    /**
     * Retrieve the use statements from the method annotations.
     *
     * @return array<string, class-string>
     */
    public function fromMethodAnnotations(): array
    {
        $useStatements = [];

        foreach ($this->inspector->methodAnnotations($this->includeExisting) as $annotation) {
            foreach ($annotation->namespaces as $namespace) {
                if (! $this->inspector->hasSameNamespace($namespace)) {
                    $useStatements[class_basename($namespace)] = $namespace;
                }
            }
        }

        return $useStatements;
    }
}
