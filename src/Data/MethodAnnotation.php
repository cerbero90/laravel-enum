<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Data;

use BackedEnum;
use Cerbero\Enum\Data\MethodAnnotation as BaseMethodAnnotation;
use ReflectionMethod;

use function Cerbero\LaravelEnum\namespaceExists;

/**
 * The method annotation.
 */
final class MethodAnnotation extends BaseMethodAnnotation
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
     * Retrieve the method annotation for the given case and capsule.
     *
     * @param class-string $capsule
     */
    public static function forCapsule(BackedEnum $case, string $capsule): static
    {
        preg_match_all(self::RE_ARGUMENTS, (string) $case->value, $matches);

        $capsuleName = class_basename($capsule);
        $arguments = implode(', ', array_map('trim', $matches[1]));

        return new self($case->name, "static {$capsuleName} {$case->name}({$arguments})", [$capsule]);
    }

    /**
     * Retrieve the method annotation for the given invokable class.
     *
     * @param class-string $class
     */
    public static function forInvokable(string $name, string $class): static
    {
        $parameters = $namespaces = [];
        $reflection = new ReflectionMethod($class, '__invoke');
        $returnType = (string) $reflection->getReturnType() ?: 'mixed';

        if (namespaceExists($returnType)) {
            /** @var class-string $returnType */
            $namespaces[] = $returnType;

            $returnType = class_basename($returnType);
        }

        foreach ($reflection->getParameters() as $parameter) {
            $type = (string) $parameter->getType() ?: 'mixed';

            if (namespaceExists($type)) {
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
    public static function instance(string $name, string $returnType): static
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
    public static function forTranslation(string $name, string $translation): static
    {
        preg_match_all(self::RE_PLACEHOLDER, $translation, $matches);

        $parameters = array_map(fn(string $name) => "mixed \${$name}", $matches[1]);

        return new self($name, sprintf('string %s(%s)', $name, implode(', ', $parameters)));
    }
}
