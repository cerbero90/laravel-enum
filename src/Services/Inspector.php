<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Services;

use Cerbero\Enum\Services\Inspector as BaseInspector;
use Cerbero\LaravelEnum\Capsules;
use Cerbero\LaravelEnum\Concerns;
use Cerbero\LaravelEnum\Data\MethodAnnotation;
use UnitEnum;

/**
 * The enum inspector.
 *
 * @template TEnum of UnitEnum
 *
 * @extends BaseInspector<TEnum>
 */
final class Inspector extends BaseInspector
{
    /**
     * The main trait to supercharge enums.
     *
     * @var class-string
     */
    protected string $mainTrait = Concerns\Enumerates::class;

    /**
     * The capsules keyed by the related trait.
     *
     * @var array<class-string, class-string>
     */
    private array $capsules = [
        Concerns\EnumeratesCacheKeys::class => Capsules\CacheKey::class,
        Concerns\EnumeratesSessionKeys::class => Capsules\SessionKey::class,
    ];

    /**
     * Determine whether the given namespace matches the enum namespace.
     */
    public function hasSameNamespace(string $namespace): bool
    {
        return $this->reflection->getNamespaceName() . '\\' . class_basename($namespace) == $namespace;
    }

    /**
     * Retrieve the method annotation for the given case.
     */
    public function caseAnnotation(UnitEnum $case): MethodAnnotation
    {
        foreach ($this->capsules as $trait => $capsule) {
            if ($this->uses($trait)) {
                /** @var \BackedEnum $case */
                return MethodAnnotation::forCapsule($case, $capsule);
            }
        }

        return MethodAnnotation::forCase($case);
    }

    /**
     * Retrieve the use statements.
     *
     * @return array<string, class-string>
     */
    public function useStatements(bool $includeExisting = true): array
    {
        return $this->useStatements ??= [...new UseStatements($this, $includeExisting)];
    }

    /**
     * Retrieve the method annotations.
     *
     * @return array<string, MethodAnnotation>
     */
    public function methodAnnotations(bool $includeExisting = true): array
    {
        /** @var array<string, MethodAnnotation> */
        return $this->methodAnnotations ??= [...new MethodAnnotations($this, $includeExisting)];
    }
}
