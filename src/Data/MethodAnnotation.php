<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Data;

use BackedEnum;
use Cerbero\LaravelEnum\Services\CacheKey;
use ReflectionMethod;
use Stringable;
use UnitEnum;

use function Cerbero\LaravelEnum\namespaceExists;

/**
 * The method annotation.
 */
final class MethodAnnotation implements Stringable
{
    /**
     * The regular expression to extract the explicit arguments.
     */
    public const RE_ARGUMENTS = '~{([a-z]+\s+\$[^}]+)}~';

    /**
     * The regular expression to extract the translation placeholders.
     */
    public const RE_PLACEHOLDER = '~:([a-zA-Z0-9_]+)~';

    /**
     * Whether the method is static.
     */
    public readonly bool $isStatic;

    /**
     * Retrieve the method annotation for the given case.
     */
    public static function forCase(UnitEnum $case): self
    {
        $returnType = is_int($case->value ?? null) ? 'int' : 'string';

        return new self($case->name, "static {$returnType} {$case->name}()");
    }

    /**
     * Retrieve the method annotation for the given cache case.
     */
    public static function forCacheCase(BackedEnum $case): self
    {
        preg_match_all(self::RE_ARGUMENTS, (string) $case->value, $matches);

        $arguments = implode(', ', array_map('trim', $matches[1]));

        return new self($case->name, "static CacheKey {$case->name}({$arguments})", [CacheKey::class]);
    }

    /**
     * Retrieve the method annotation for the given invokable class.
     *
     * @param class-string $class
     */
    public static function forInvokable(string $name, string $class): self
    {
        $parameters = $namespaces = [];
        $reflection = new ReflectionMethod($class, '__invoke');

        if (namespaceExists($returnType = (string) $reflection->getReturnType() ?: 'mixed')) {
            /** @var class-string $returnType */
            $namespaces[] = $returnType;

            $returnType = class_basename($returnType);
        }

        foreach ($reflection->getParameters() as $parameter) {
            if (namespaceExists($type = (string) $parameter->getType() ?: 'mixed')) {
                /** @var class-string $type */
                $namespaces[] = $type;

                $type = class_basename($type);
            }

            $parameters[] = "{$type} \${$parameter->getName()}";
        }

        return new self($name, sprintf('%s %s(%s)', $returnType, $name, implode(', ', $parameters)), $namespaces);
    }

    /**
     * Retrieve the method annotation for an instance method.
     */
    public static function instance(string $name, string $returnType): self
    {
        $namespaces = [];
        $null = str_starts_with($returnType, '?') ? '?' : '';
        $returnType = ltrim($returnType, '?');

        if (namespaceExists($returnType)) {
            /** @var class-string $returnType */
            $namespaces[] = $returnType;

            $returnType = class_basename($returnType);
        }

        return new self($name, "{$null}{$returnType} {$name}()", $namespaces);
    }

    /**
     * Retrieve the method annotation for the given translation.
     */
    public static function forTranslation(string $name, string $translation): self
    {
        preg_match_all(self::RE_PLACEHOLDER, $translation, $matches);

        $parameters = array_map(fn(string $name) => "mixed \${$name}", $matches[1]);

        return new self($name, sprintf('string %s(%s)', $name, implode(', ', $parameters)));
    }

    /**
     * Instantiate the class.
     *
     * @param list<class-string> $namespaces
     */
    public function __construct(
        public readonly string $name,
        public readonly string $annotation,
        public readonly array $namespaces = [],
    ) {
        $this->isStatic = str_starts_with($annotation, 'static');
    }

    /**
     * Retrieve the method annotation string.
     */
    public function __toString(): string
    {
        return "@method {$this->annotation}";
    }
}
