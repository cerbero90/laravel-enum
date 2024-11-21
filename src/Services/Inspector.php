<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\LaravelEnum\Concerns\EnumeratesCacheKeys;
use Cerbero\LaravelEnum\Data\MethodAnnotation;
use ReflectionEnum;

/**
 * The enum inspector.
 *
 * @template TEnum
 */
final class Inspector
{
    /**
     * The enum reflection.
     *
     * @var ReflectionEnum<\UnitEnum>
     */
    private readonly ReflectionEnum $reflection;

    /**
     * The method annotations.
     *
     * @var array<string, MethodAnnotation>
     */
    private array $methodAnnotations;

    /**
     * The use statements.
     *
     * @var array<string, class-string>
     */
    private array $useStatements;

    /**
     * Instantiate the class.
     *
     * @param class-string<TEnum> $enum
     */
    public function __construct(
        private readonly string $enum,
        private readonly bool $force,
    ) {
        /** @var class-string<\UnitEnum> $enum */
        $this->reflection = new ReflectionEnum($enum);
    }

    /**
     * Retrieve the enum filename.
     */
    public function filename(): string
    {
        return (string) $this->reflection->getFileName();
    }

    /**
     * Retrieve the enum namespace.
     */
    public function namespace(): string
    {
        return $this->reflection->getNamespaceName();
    }

    /**
     * Determine whether the given namespace matches the enum namespace.
     */
    public function hasSameNamespace(string $namespace): bool
    {
        return $namespace == $this->namespace() . '\\' . class_basename($namespace);
    }

    /**
     * Retrieve the DocBlock of the enum.
     */
    public function docBlock(): string
    {
        return $this->reflection->getDocComment() ?: '';
    }

    /**
     * Retrieve the enum cases.
     *
     * @return list<TEnum>
     */
    public function cases(): array
    {
        /** @var list<TEnum> */
        return $this->enum::cases();
    }

    /**
     * Retrieve the meta attribute names of the enum.
     *
     * @return list<string>
     */
    public function metaAttributeNames(): array
    {
        /** @var list<string> */
        return $this->enum::metaAttributeNames();
    }

    /**
     * Determine whether the enum enumerates cache keys.
     */
    public function enumeratesCacheKeys(): bool
    {
        return $this->uses(EnumeratesCacheKeys::class);
    }

    /**
     * Determine whether the enum uses the given trait.
     */
    public function uses(string $trait): bool
    {
        return isset($this->traits()[$trait]);
    }

    /**
     * Retrieve all the enum traits.
     *
     * @return array<class-string, class-string>
     */
    public function traits(): array
    {
        $traits = [];

        foreach ($this->reflection->getTraitNames() as $trait) {
            $traits += [$trait => $trait, ...trait_uses_recursive($trait)];
        }

        /** @var array<class-string, class-string> */
        return $traits;
    }

    /**
     * Retrieve the use statements.
     *
     * @return array<string, class-string>
     */
    public function useStatements(): array
    {
        return $this->useStatements ??= [...new UseStatements($this)];
    }

    /**
     * Retrieve the method annotations.
     *
     * @return array<string, MethodAnnotation>
     */
    public function methodAnnotations(): array
    {
        return $this->methodAnnotations ??= [...new MethodAnnotations($this, $this->force)];
    }
}
